/*
 * utility to send a LNT file to a Monarch 9417+ printer
 */
#include <stdlib.h>
#include <stdint.h>
#include <stdio.h>
#include <errno.h>
#include <string.h>
#include <sys/types.h>
#include <netinet/in.h>
#include <sys/wait.h>
#include <sys/socket.h>
#include <dirent.h>
// #include <unistd.h> // here for sleep but causes lots of other issues :-(

#define DEBUG 2

// parts in job file
#define JOB_NAME	0x01
#define JOB_IP		0x02
#define JOB_PORT	0x04
#define JOB_LABEL	0x08

#define JOB_ALL		0x0f

// holder for job parts
typedef struct {
	char name[100];
	char ip[100];
	char port[100];
	char label[100];
	int status;
} job_details;

// defintions for left and right printers on Monarch 9417+
#define LEFTPORT 9100
#define RIGHTPORT 9102

#define LENGTH 0xFFFF // Buffer length

static int _printing_enabled = 1;	// on by default but can be cleared in settings file

static char *decoding_table = NULL;
static int mod_table[] = {0, 2, 1};

/*
 * fn to trim of extar whitespace and carrige returns
 */
void trim(char *s)
{
	int i;
	int len = strlen(s);
	for (i=len-1;i>0;i--) 
		if ((s[i] == '\n') || (s[i] == '\r') || (s[i] == ' ') || (s[i] == '\t')) 
			s[i] = '\0';
		else 
			break;
}

int send_raw (char *ip, int port, char *data, int data_size, int copies)
{
    int sockfd; // Socket file descriptor
    int num;
    int sin_size; // to store struct size
    struct sockaddr_in remote_addr;
	char header[100];
	int stat;
	int i;

    /* Get the Socket file descriptor */
    if( (sockfd = socket(AF_INET, SOCK_STREAM, 0)) == -1 )
    {
        printf ("ERROR: Failed to obtain Socket Descriptor.\n");
        return (0);
    }
    else
	printf ("obtain socket descriptor successfully.\n");

    /* Fill the socket address struct */
    remote_addr.sin_family = AF_INET;
    remote_addr.sin_port = htons(port);
    inet_pton(AF_INET, ip, &remote_addr.sin_addr);
    bzero(&(remote_addr.sin_zero), 8);
    /* Try to connect the remote */
    if (connect(sockfd, (struct sockaddr *)&remote_addr, sizeof(struct sockaddr)) == -1)
    {
        printf ("ERROR: Failed to connect to the printer @ %s! %s\n",ip, strerror(errno));
        return (0);
    }
    else
	printf("connected to printer at port %d...ok!\n", port);


    for (i = 0; i < copies; i++) {
    	stat = send(sockfd, data, data_size, 0);
    	printf("Sent %d of %d size %d data (%d)\n",i,copies,stat,data_size);
    }
    if (stat < 0) 
	printf("ERROR: Failed to send %d bytes of LNT data. %s\n", data_size,strerror(errno));


	// data sent, now read any response
		/*
    bzero(header, 100);
    stat = recv(sockfd, header, 100, 0);
	printf("Response (%d): %s\n",stat,header);
*/
    printf("connection closed.\n");
    close(sockfd);

    return stat;
}



/*
 * fn to load input LBL file
 */
char *get_input_LNT(char *f_name, int *input_LNT_len, int *fields)
{
    char sdbuf[LENGTH]; // Send buffer
    FILE *fp = fopen(f_name, "r");
    static char p[2000];
    *input_LNT_len = 0;

    if(fp == NULL)
    {
        printf("ERROR: File %s not found.\n", f_name);
        return 0;
    }

    bzero(sdbuf, LENGTH);
    int f_block_sz;
    int fsize = 0;
    while((f_block_sz = fread(&sdbuf[fsize], sizeof(char), LENGTH, fp))>0)
    {
		// SHOULD REALLY HAVE MAX FILE SIZE CHECKING HERE !!!
		fsize += f_block_sz;
    }
	fclose(fp);
	printf("LBL file size %d\n",fsize);

	// check for FIELDS definition - not used for 9419
	{
		char *s;
		*fields = 0;
		if ((s = strstr(sdbuf,"###FIELDS:")))
		{
			*fields = atoi(&s[10]);
			printf("fields %d\n",*fields);
		}
	}

	// char *p = malloc(fsize+1);
	memcpy(p,sdbuf, fsize+1);
	p[fsize] = 0;
	*input_LNT_len = fsize;
	return p;
}

/* 
 * fn tpo search a buf for a string and replace it. String has ## added before and after
 */
int find_and_replace(char *buf, char *key, char *val)
{
	char fullkey[100];
	char *s;
	int n = 0;
	sprintf(fullkey,"##%s##",key);

	while ((s = strstr(buf,fullkey))) {
		n++;

		// now paste in data
		int keylen = strlen(fullkey);
		int vallen = strlen(val);
		//printf("%s %d %d\n",fullkey,keylen,vallen);

		// handle based on length differences
		if (keylen == vallen)
		{
			memcpy(s,val,vallen);
			//printf("%20s\n",(s-2));
		}
		else if (keylen > vallen) // lose bytes
		{
			memcpy(s,val,vallen);
			// now shift down remaining bytes
			char *q = s+keylen; // start of remaining data
			char *p = s+vallen;	// end of new data
			do { *p++ = *q++; } while (*q != '\0');
			*p = '\0';
			//printf("%20s\n",(s-2));
		}
		else //gain bytes
		{
			// need to move bytes first
			char *end = s+keylen; // start of remianing data
			char *p = s + strlen(s) + (vallen - keylen);	// get to end of data plus extra bits
			char *q = s + strlen(s);	// get to end of data plus extra bits
			do { *p-- = *q--; } while (q >=  end);
			memcpy(s,val,vallen);
			//printf("%20s\n",(s-2));
		}
	}
	return n;
}

/*
 * function to parse a job file
 */
int parse_job_file(char *jobfile, char *labeldir)
{
	char sdbuf[1000]; // line buffer
	// input LNT
   	char *input_LNT;
   	int	 input_LNT_len;
	int	 LNT_fields = 10; // max fields
	int label_count = 0;
	job_details *jstr, job_setup;

	jstr = &job_setup;

    FILE *fp = fopen(jobfile, "r");

	jstr->status = 0; // state flags covering all the found bits

    if(fp == NULL)
    {
        printf("ERROR: Job File %s not found.\n", jobfile);
        return 0;
    }

	while(fgets(sdbuf, 1000, fp) != NULL)
    {
		if (DEBUG > 1) printf("%s",sdbuf);
		// skip comments
		if (sdbuf[0] != '#')
		{
			if (!strncmp(sdbuf,"Jobname:",8))
			{
				strcpy(jstr->name,&sdbuf[8]);
				trim(jstr->name);
				jstr->status |= JOB_NAME;
			}
			else if (!strncmp(sdbuf,"Printer:",8))
			{
				strcpy(jstr->ip,&sdbuf[8]);
				trim(jstr->ip);
				jstr->status |= JOB_IP;
			}
			else if (!strncmp(sdbuf,"Port:",5))
			{
				strcpy(jstr->port,&sdbuf[5]);
				trim(jstr->port);
				jstr->status |= JOB_PORT;
			}
			else if (!strncmp(sdbuf,"Label:",6))
			{
				strcpy(jstr->label,&sdbuf[6]);
				trim(jstr->label);
				jstr->status |= JOB_LABEL;
			}
			else if (!strncmp(sdbuf,"Endheader",9))
			{
				break;
			}
		}
    }

	if (jstr->status != JOB_ALL)
	{	
		printf("Incorrect file format\n");
		close(fp);
		return 0;
	}

	// got this far so assume file is ok now get the input label format
	// read in input LNT file
	// append default label directory
	sprintf(sdbuf,"%s/%s",labeldir,jstr->label);
	input_LNT = get_input_LNT(sdbuf, &input_LNT_len, &LNT_fields);
	if (!input_LNT_len) 
	{
	    printf("Failed to load LNT file %s\n",sdbuf);
		close(fp);
	    return 0;
	}
	/*
	if (!LNT_fields) 
	{
		printf("LNT file %s does not contain ###FIELDS entry\n",jstr->label);
		close(fp);
	    return 0;
	}

	*/
	// at this point we have all the job data plus a lnt file and the number of fields to match
	// now scan in the each field from the file and see if we get matches and update the current form
//	while (!feof(fp))
	if (!feof(fp))
	{
		// make a copy of the data
		int len = input_LNT_len + 100*LNT_fields;
		char tlnt[2000];
		bzero(tlnt, 2000);
		memcpy(tlnt,input_LNT,input_LNT_len);
		int copies = 0;
		int ffound = 0;
		// base64 encode final label
		char *enc_LNT;
		int	 enc_LNT_len = 0;


		printf("\nProcessing Label %d\n",++label_count);

		while(fgets(sdbuf, 1000, fp) != NULL)
		{
			if (DEBUG > 1) printf(">%s",sdbuf);
			// skip comments
			if (sdbuf[0] != '#') {
				if (!strncmp(sdbuf,"Copies:",7))
				{
					copies = atoi(&sdbuf[7]);
				}
				else if (!strncmp(sdbuf,"Endlabel",8))
				{
					break;
				} 
				else // field parsing
				{
					// split into parts
					trim(sdbuf);
					char *val = strstr(sdbuf,":");
					*val++ = '\0';
					printf("%s:%s\n",sdbuf,val);
					if (!find_and_replace(tlnt,sdbuf,val))
					{
						printf("Cannot find key %s\n",sdbuf);
						// close(fp);
						// free(tlnt);
						// free(input_LNT);
						// return 0;
					}
					else
						ffound++;
				}

				if (copies == 0)
				{
					printf("Illegal copies entry %d\n",copies);
					close(fp);
					// free(tlnt);
					// free(input_LNT);
					return 0;
				}
			}
		}

		if (ffound != LNT_fields)
		{
			printf("incorrect number of fields %d <> %d\n",ffound ,LNT_fields);
/*			close(fp);
			free(tlnt);
			free(input_LNT);
			return 0; */
		}
		printf("[%s]\n",tlnt);

		// all ok some time to print it
		// now encode it
		int tlnt_len = strlen(tlnt);
		if (_printing_enabled) send_raw(jstr->ip, atoi(jstr->port), tlnt, tlnt_len, copies);

		// free(tlnt);
		copies = 0;
		ffound = 0;
	}

	// free(input_LNT);
	close(fp);

	return 1;
}

/*
 * fn to check a directory looking for job files
 */
int check_jobs_exists(char *prn_dir, char *labeldir)
{
	DIR *dir;
	struct dirent *ent;
	int found = 0;

	if ((dir = opendir (prn_dir)) != NULL)
	{
		// printf("opened %d\n",dirfd(dir));
		// print all the files and directories within directory
		while ((ent = readdir (dir)) != NULL)
		{
			if (strstr(ent->d_name,".job"))
			{
				char fname[200];
				sprintf(fname,"%s/%s",prn_dir,ent->d_name);
				printf("Processing %s\n",fname);
				parse_job_file(fname,labeldir);
				if (remove(fname) !=0)
				{
					printf("Error removing %s\n",fname);
					return 0;
				}
			}
		}
		// printf("closed\n");
		closedir(dir);
	}
	else
	{
		printf("Cannot open job dir %d\n",errno);
		return 0;
	}

	return 1;
}

/*
 * function to parse settings file
 */
int parse_settings_file(char *settingsfile, char *jobdir, char *labeldir, int *print)
{
	FILE *fp = fopen(settingsfile, "r");
	char sdbuf[1000]; // line buffer
	labeldir[0] = jobdir[0] = '\0';

	if(fp == NULL)
    {
        printf("ERROR: Settings File %s not found.\n", settingsfile);
        return 0;
    }

	while(fgets(sdbuf, 1000, fp) != NULL)
    {
		//if (DEBUG > 1) printf("%s",sdbuf);
		// skip comments
		if (sdbuf[0] != '#')
		{
			trim(sdbuf);
			if (!strncmp(sdbuf,"JobDir:",7))
				strcpy(jobdir,&sdbuf[7]);
			else if (!strncmp(sdbuf,"LabelDir:",9))
				strcpy(labeldir,&sdbuf[9]);
			else if (!strncmp(sdbuf,"Print:",6))
				*print = (sdbuf[6] == '1');
		}
	}
	fclose(fp);

	if ((labeldir[0] == '\0') || (jobdir[0] == '\0'))
	{
		printf("ERROR: FInding settings JobDir:%s LabelDir:%s\n",jobdir,labeldir);
        return 0;
	}
	return 1;
}


int main (int argc, char *argv[])
{
	char jobdir[200];
	char labeldir[200];

	if (argc!=2) 
    {
	    printf("Usage: print_lnt jobfile.job\n");
	    return 0;
    }

	// get settings
	if (!parse_settings_file(argv[1],jobdir, labeldir, &_printing_enabled)) return 0;

	printf("JobDir:%s\nLabelDir:%s\nPrinting:%d\n",jobdir,labeldir,_printing_enabled);

	while (1)
	{
		if (!check_jobs_exists(jobdir,labeldir)) sleep(2);
		sleep(2);
	}

	return 1;
}

