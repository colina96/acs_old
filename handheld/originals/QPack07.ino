#include <Adafruit_MLX90614.h>
#include <Adafruit_MAX31855.h>
#include <EEPROM.h>

/*
 * Firmware for QAmC QPack device with ATmega328PB mcu
 * 
 * Arduino Board Selection
 * Board:   Pololu A-Star 328PB
 * Version: 3.3V, 8MHz
 * Baud:    57600
 * 
 * Modified 25 Feb 2019 to add EEPROM calibration data & extra commands
 * Modified 31 Jan 2019 for QPack R3.2 & R3.3 PCB
 * 
 * Probably need to customise Adafruit_MLX90614 to use I2C instead of Wire library
 * As the Wire library doesnt implement any timeout
 * 
 * (c) February 2019 QAmC & Kean Electronics
 */

#define VERSION "QPACK07"

/* pin definitions */
const int phone_txd  = 0;   // PD0 Serial receive from Android
const int phone_rxd  = 1;   // PD1 Serial transmit to Android
const int trig_sw    = 2;   // PD2 goes low when trigger pressed
const int thumb_sw   = 3;   // PD3 goes low when thumb button pressed
const int scan_read  = 4;   // PD4 goes high when successfull scan
const int scan_trig  = 5;   // PD5 set high to begin barcode scan
const int probe_sw   = 6;   // PD6 goes low when probe folded in
const int laser_en   = 7;   // PD7 set low to enable laser pointer
const int scan_en    = 8;   // PB0 set low to power on barcode scanner
const int sensor_en  = 9;   // PB1 set low to power on sensors
const int beep_en    = 10;  // PB2 set low to BEEEEP!  (if installed)
const int scan_rxd   = 11;  // PB3 Serial1 transmit to barcode scanner
const int scan_txd   = 12;  // PB4 Serial1 receive from barcode scanner
const int usbid_en   = 13;  // PB5 set high to drive USB OTG ID pin low (& keep power on)
const int spi1_MISO  = 14;  // PC0 data sent from MAX31855 thermocouple chip
const int spi1_SCK   = 15;  // PC1 clock to MAX31855 thermocouple chip
const int vchg_sense = A2;  // PC2/ADC2 USB 5V in from charger via 10k/10k divider
const int vbat_sense = A3;  // PC3/ADC3 USB 5V in from charger via 10k/10k divider
const int i2c0_sda   = 18;  // PC4 I2C SDA to MLX90614
const int i2c0_scl   = 19;  // PC5 I2C SCL to MLX90614
const int spi1_CS    = 20;  // PE2 set low to enable MAX31855 thermocouple chip
const int spi1_MOSI  = 21;  // PE3 unused
const int chg_en    = SDA1; // PE0 set high to enable phone charging (usbid_en must be low)
const int chg_stat  = SCL1; // PE1 goes low while 2nd battery charging (open drain, needs pullup)

// analog inputs for charge and battery voltage are divide by 2, so 1023 = 6.6V
// 700*6.6/1023 = 4.516 and 650*6.6/1023 = 4.193
#define CHARGE_THRESHOLD  700   // about 4.5V
#define CHARGE_LOSS       650   // about 4.2V

#define SLEEP_MSEC_DEF    300000L

#define BC_TIME           3000
#define MAX_BC            20

#define NUM_PARAM         5     // number of config parameters

char c, rxln;
char rxBC[MAX_BC+1];

bool trig, thumb, probe, scan; //, chg;
bool old_trig = HIGH;
bool old_thumb = HIGH;
bool old_probe = HIGH;
bool old_scan = LOW;
//bool old_chg = HIGH;

bool scanning = false;

int bat_v;
int charge_v;
bool charging = false;

bool calmode = false;
bool finemode = false;
long sleep_msec = SLEEP_MSEC_DEF;
float offset_tc = 0.0;
float offset_mlx = 0.0;

int x = 0;
int y = 0;

unsigned long bc_to = 0;    // timer for barcode scan
unsigned long sleepy = 0;   // timer for no activity sleep

Adafruit_MLX90614 mlx = Adafruit_MLX90614();

Adafruit_MAX31855 thermocouple(spi1_SCK, spi1_CS, spi1_MISO);

void load_config() {
  int sn, hw, it, tc, mlx;
  EEPROM.get(sizeof(int) * 0, sn);
  EEPROM.get(sizeof(int) * 1, hw);
  EEPROM.get(sizeof(int) * 2, it);
  EEPROM.get(sizeof(int) * 3, tc);
  EEPROM.get(sizeof(int) * 4, mlx);
  if (sn<0 || hw <0) return;
  if (it<0) sleep_msec = SLEEP_MSEC_DEF;
  else sleep_msec = it * 1000L;
  offset_tc  = (float)tc / 100.0;
  offset_mlx = (float)mlx / 100.0;
}

void setup() {
  // ensure power stays on for now via OTG
  pinMode(usbid_en, OUTPUT);
  digitalWrite(usbid_en, HIGH);

  // and phone charging is off
  pinMode(chg_en, OUTPUT);
  digitalWrite(chg_en, LOW);

  // configure other outputs
  pinMode(scan_trig, OUTPUT);
  digitalWrite(scan_trig, LOW);
  pinMode(laser_en, OUTPUT);
  digitalWrite(laser_en, HIGH);
  pinMode(scan_en, OUTPUT);
  digitalWrite(scan_en, HIGH);
  pinMode(sensor_en, OUTPUT);
  digitalWrite(sensor_en, HIGH);
  pinMode(beep_en, OUTPUT);
  digitalWrite(beep_en, HIGH);
  pinMode(spi1_CS, OUTPUT);
  digitalWrite(spi1_CS, HIGH);

  // input pins with pullup
  pinMode(trig_sw, INPUT_PULLUP);
  //digitalWrite(trig_sw, HIGH);  // pullup
  pinMode(thumb_sw, INPUT_PULLUP);
  //digitalWrite(thumb_sw, HIGH);  // pullup
  pinMode(probe_sw, INPUT_PULLUP);
  //digitalWrite(probe_sw, HIGH);  // pullup
  pinMode(scan_read, INPUT);
  digitalWrite(scan_read, LOW);  // no pullup
  pinMode(chg_stat, INPUT_PULLUP);
  //digitalWrite(chg_stat, HIGH);  // pullup

  // with 8MHz crystal max baudrate is 57600
  Serial.begin(57600);  // to Android
  Serial1.begin(9600);  // to barcode scanner

  delay(250);
  Serial.println(VERSION);

  rxln = 0;
  rxBC[rxln] = 0;

  load_config();
    
  mlx.begin();
  thermocouple.begin();

  kick();
}

void kick() {
  sleepy = millis() + sleep_msec;
  if (!charging) digitalWrite(usbid_en, HIGH);
}

void nitenite() {
  kick();
  if (charging) return;
  Serial.println("zzz");

  digitalWrite(scan_trig, LOW);
  scanning = false;
  digitalWrite(scan_en, HIGH);
  digitalWrite(sensor_en, HIGH);
  digitalWrite(laser_en, HIGH);
  digitalWrite(beep_en, HIGH);
  
  delay(20);
  digitalWrite(usbid_en, LOW);

  delay(200);
}

void loop() {
  trig  = digitalRead(trig_sw);
  thumb = digitalRead(thumb_sw);
  probe = digitalRead(probe_sw);
  scan  = digitalRead(scan_read);
  //chg   = digitalRead(chg_stat);

  if (trig==LOW && thumb==LOW) {
    nitenite();
    while (trig==LOW || thumb==LOW) {
      delay(100);
      trig  = digitalRead(trig_sw);
      thumb = digitalRead(thumb_sw);
    }
    delay(1000);
    old_trig  = trig;
    old_thumb = thumb;
    Serial.println("-T");
    Serial.println("-B");
  }
  if (trig!=old_trig) {
    kick();
    if (trig==LOW) Serial.println("+T");
    else Serial.println("-T");
  }
  if (thumb!=old_thumb) {
    kick();
    if (thumb==LOW) Serial.println("+B");
    else Serial.println("-B");
  }
  if (probe!=old_probe) {
    kick();
    if (probe==LOW) Serial.println("+P");
    else Serial.println("-P");
  }
  if (scan!=old_scan) {
    kick();
    //if (scan==LOW) Serial.println("+S");
    //else Serial.println("-S");
  }
  /*
  if (chg!=old_chg) {
    //kick();
    if (chg==LOW) Serial.println("+U");
    else Serial.println("-U");
  }
  */
  
  old_trig  = trig;
  old_thumb = thumb;
  old_probe = probe;
  old_scan  = scan;
  //old_chg   = chg;

  charge_v = analogRead(vchg_sense);
  if (!charging && charge_v>CHARGE_THRESHOLD) {
    // enable charging
    digitalWrite(usbid_en, LOW);
    delay(50);
    digitalWrite(chg_en, HIGH);
    Serial.println("+C");
    charging = true;
  } else if (charging && charge_v<CHARGE_LOSS) {
    // disable charging
    digitalWrite(chg_en, LOW);
    // don't reconnect OTG - doing it too soon causes problems
    delay(50);
    //digitalWrite(usbid_en, HIGH);
    Serial.println("-C");
    charging = false;
  }

  if (Serial.available()>0) {
    kick();
    c = Serial.read();
    if (c=='Z') {
      nitenite();
    }
    else if (c=='S') {
      Serial.println(c);
      digitalWrite(scan_trig, HIGH);
      bc_to = millis() + BC_TIME;
      scanning = true;
    }
    else if (c=='s') {
      Serial.println(c);
      digitalWrite(scan_trig, LOW);
      scanning = false;
    }
    else if (c=='B') {
      Serial.println(c);
      digitalWrite(scan_en, LOW);
    }
    else if (c=='b') {
      Serial.println(c);
      scanning = false;
      digitalWrite(scan_trig, LOW);
      digitalWrite(scan_en, HIGH);
    }
    else if (c=='L') {
      Serial.println(c);
      digitalWrite(laser_en, LOW);
    }
    else if (c=='l') {
      Serial.println(c);
      digitalWrite(laser_en, HIGH);
    }
    else if (c=='P') {
      Serial.println(c);
      digitalWrite(sensor_en, LOW);
    }
    else if (c=='p') {
      Serial.println(c);
      digitalWrite(sensor_en, HIGH);
    }
    else if (c=='t') {
      Serial.print(c);
      if (digitalRead(sensor_en)==HIGH) {
        digitalWrite(sensor_en, LOW);
        delay(500);
      }
      //Serial.print(mlx.readAmbientTempC());
      //Serial.print(",");
      float t = mlx.readObjectTempC();
      if (!calmode) t += offset_mlx;
      if (finemode) Serial.println(t,2);
      else Serial.println(t,1);
    }
    else if (c=='T') {
      Serial.print(c);
      if (digitalRead(sensor_en)==HIGH) {
        digitalWrite(sensor_en, LOW);
        delay(500);
      }
      //Serial.print(thermocouple.readInternal());
      //Serial.print(",");
      float t = thermocouple.readCelsius();
      if (!calmode) t += offset_tc;
      if (finemode) Serial.println(t,2);
      else Serial.println(t,1);
    }
    else if (c=='!') {
      analogWrite(beep_en, 0x55);
      delay(250);
      analogWrite(beep_en, 0);
      delay(5);
      digitalWrite(beep_en, HIGH);
    }
    else if (c=='?') {
      float v;
      bat_v = analogRead(vbat_sense);
      bat_v = analogRead(vbat_sense);
      v = bat_v * 6.6 / 1023;
      Serial.print(c);
      Serial.print(bat_v);
      Serial.print(',');
      Serial.print(v,1);
      Serial.println();
      charge_v = analogRead(vchg_sense);
    }
    else if (c=='V') {
      Serial.print(c);
      Serial.println(VERSION);
    }
    else if (c=='C') {
      Serial.println(c);
      calmode = true;
    }
    else if (c=='c') {
      Serial.println(c);
      calmode = false;
    }
    else if (c=='F') {
      Serial.println(c);
      finemode = true;
    }
    else if (c=='f') {
      Serial.println(c);
      finemode = false;
    }
    else if (c==':') {
      x = y = 0;
    }
    else if (c==',') {
      y = x;
      x = 0;
    }
    else if (c=='-') {
      x = -x;
    }
    else if (c>='0' && c<='9') {
      x = x * 10 + (c - '0');
    }
    else if (c=='R' && x>0 && x<=NUM_PARAM) {
      int v;
      EEPROM.get(sizeof(int) * (x-1), v);
      Serial.print(c);
      Serial.print(x);
      Serial.print(',');
      Serial.print(v);
      Serial.println();
      x = y = 0;
    }
    else if (c=='W' && y>0 && y<=NUM_PARAM) {
      int v;
      EEPROM.put(sizeof(int) * (y-1), x);
      EEPROM.get(sizeof(int) * (y-1), v);
      Serial.print(c);
      Serial.print(y);
      Serial.print(',');
      Serial.print(v);
      Serial.println();
      load_config();
      kick();
      x = y = 0;
    }
  }

  if (Serial1.available()>0) {
    kick();
    c = Serial1.read();
    if (isprint(c)) {
      rxBC[rxln++] = c;
      rxBC[rxln] = 0;
    }
    if ((c=='\r' && rxln>0) || (rxln==MAX_BC)) {
      Serial.print('[');
      Serial.print(rxBC);
      Serial.println(']');
      digitalWrite(scan_trig, LOW);
      scanning = false;
      rxln = 0;
      rxBC[rxln] = 0;
    }
  }

  if (scanning && (long)(millis() - bc_to)>=0) {
      Serial.println('.');
      digitalWrite(scan_trig, LOW);
      scanning = false;
  }
  
  if (sleep_msec!=0 && (long)(millis() - sleepy)>=0) {
    nitenite();
  }
}
