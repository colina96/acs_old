/* fpont 12/99 */
/* pont.net    */
/* udpServer.c */

#include <sys/types.h>
#include <sys/socket.h>
#include <netinet/in.h>
#include <arpa/inet.h>
#include <ifaddrs.h>
#include <netdb.h>
#include <stdio.h>
#include <unistd.h> /* close() */
#include <string.h> /* memset() */



//for Mac OS X
#include <stdlib.h>

#define UDP_PORT 1500
#define UDP_RESP 1501
#define MAX_MSG 100

int send_response(int sd,char *client,int port, char *response_msg)
{
	   /* send response */
	struct sockaddr_in cliAddr, remoteServAddr;
	struct hostent *h;
	// char *host = "255.255.255.255"; // broadcast - should only get as far as a switch or router????
	int rc,i;

	h = gethostbyname(client);
	if(h==NULL) {
		printf("unknown host '%s' \n", client);
		exit(1);
	}


	printf("sending data to '%s' (IP : %s) \n", h->h_name,
		 inet_ntoa(*(struct in_addr *)h->h_addr_list[0]));

	remoteServAddr.sin_family = h->h_addrtype;
	memcpy((char *) &remoteServAddr.sin_addr.s_addr, h->h_addr_list[0], h->h_length);
	// remoteServAddr.sin_port = htons(UDP_PORT);
	remoteServAddr.sin_port = htons(port);
	rc = sendto(sd, response_msg, strlen(response_msg)+1, 0, (struct sockaddr *) &remoteServAddr, sizeof(remoteServAddr));

	if(rc<0) {
	  printf("cannot send data %d \n",i-1);
	  close(sd);
	  exit(1);
	}
	else {
		printf("sent %d\n",rc);
	}
}

void get_my_ip()
{
	// getaddrinfo()
}
int main(int argc, char *argv[]) {
  
  int sd, rc, n, cliLen;
  struct sockaddr_in cliAddr, servAddr;
  char msg[MAX_MSG];
  int broadcast = 1;

  char *response_fmt = "RESTHOME=http://%s/acs/REST";
  char response_msg[200] = "NOT INITIALISED";
  int client = 0;
  
  /* socket creation */
  get_my_ip();
  sd=socket(AF_INET, SOCK_DGRAM, 0);
  if(sd<0) {
    printf("%s: cannot open socket \n",argv[0]);
    exit(1);
  }
  
  if (setsockopt(sd, SOL_SOCKET, SO_BROADCAST, &broadcast,sizeof broadcast) == -1) {
          perror("setsockopt (SO_BROADCAST)");
          exit(1);
  }

  /* bind local server port */
  servAddr.sin_family = AF_INET;
  servAddr.sin_addr.s_addr = htonl(INADDR_ANY);
  servAddr.sin_port = htons(UDP_PORT);
  rc = bind (sd, (struct sockaddr *) &servAddr,sizeof(servAddr));
  if(rc<0) {
    printf("%s: cannot bind port number %d \n", 
	   argv[0], UDP_PORT);
    exit(1);
  }
  printf("bound on address %s\n",inet_ntoa(servAddr.sin_addr));
  printf("%s: waiting for data on port UDP %u\n", 
	   argv[0],UDP_PORT);
  if (argc > 1) {
	  client = 1;
	  send_response(sd,"255.255.255.255",UDP_PORT,argv[1]);
  }
  send_response(sd,"255.255.255.255",UDP_PORT,"REST_SERVER");
  /* server infinite loop */
  while(1) {
    
    /* init buffer */
    memset(msg,0x0,MAX_MSG);


    /* receive message */
    cliLen = sizeof(cliAddr);
    n = recvfrom(sd, msg, MAX_MSG, 0, 
		 (struct sockaddr *) &cliAddr, &cliLen);

    if(n<0) {
      printf("%s: cannot receive data \n",argv[0]);
      exit;
    }
      
    /* print received message */
    printf("%s: from %s:%u :  %s \n",
	   argv[0],inet_ntoa(cliAddr.sin_addr),
	   ntohs(cliAddr.sin_port),msg);
    if (!strcmp(msg,"REST_SERVER")) {
    	sprintf(response_msg,response_fmt,inet_ntoa(cliAddr.sin_addr));
    }
    //
    if (!client && strcmp(msg,response_msg)) {
    	send_response(sd,inet_ntoa(cliAddr.sin_addr),ntohs(cliAddr.sin_port),response_msg);

    	// send_response(sd,"255.255.255.255",response_msg);
    	sleep(1);
    }
  }/* end of server infinite loop */

return 0;

}
