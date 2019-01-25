print_lbl runs as a service started in /etc/rc.local

The ./rc.local file should be copied to /etc/rc.local and made executable
sudo cp rc.local /etc/rc.local
sudo chmod +x /etc/rc.local

rc.local needs to be enabled

sudo nano /etc/systemd/system/rc-local.service
Then add the following content to it.

[Unit]
 Description=/etc/rc.local Compatibility
 ConditionPathExists=/etc/rc.local

[Service]
 Type=forking
 ExecStart=/etc/rc.local start
 TimeoutSec=0
 StandardOutput=tty
 RemainAfterExit=yes
 SysVStartPriority=99

[Install]
 WantedBy=multi-user.target
Save and close the file. To save a file in Nano text editor, press Ctrl+O, then press Enter to confirm. To exit the file, Press Ctrl+X.  Next, run the following command to make sure /etc/rc.local file is executable.

sudo chmod +x /etc/rc.local
Note: Starting with 16.10, Ubuntu doesn’t ship with /etc/rc.local file anymore. You can create the file by executing this command.

printf '%s\n' '#!/bin/bash' 'exit 0' | sudo tee -a /etc/rc.local
Then add execute permission to /etc/rc.local file.

sudo chmod +x /etc/rc.local
After that, enable the service on system boot:

sudo systemctl enable rc-local

test for correctness using 
sudo /etc/rc.local start
