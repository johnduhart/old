/*
program: luggage bot

author: Andrew Caputi (and only me, muhaha)

Description:

currently:
	picks a pom-pom, and centers it in the right place to be picked up
eventually:
	places the pom-pom in appropriate bin on the robot
	moves to buckets
	dumps the pom-poms in the appropriate buckets on the playing feild
	
Revisions:
March 24 2011 -- 	discovered that previous programs issues was a result of failing robot cbc's
					and a reversal of signs in motor control
					
March 25 2011 --	rewrote my blob loops to improve performance and clarity significantly
					(by removing unecessary tershary conditionals in my whiles)
					
May 6 2011	  --	Finished robot construction (hopefully)

TODO:
design code to go from starting position to face the pom-poms
maybe break up the pom-pom cluster before trying to pick anything up - there is a problem that only happens when a pom-pom is underneath the claw
if above problem continues to happen, add a rotation between grabs to make it try for a different pom-pom
find a way to identify the robots ending position					<-hard
design code to drive from where it is to the buckets
balance the robot
swap the arm positions for over the green and pink buckets if the buckets are in backwards order - currently green is front of the robot (cbc) and pink is back
test & adjust	-_-

...crap thats a lot
*/

// camera height 120
// camera width 160

// tolerance's and range for various functions
int range = 5;			// how far the pom-pom should be from center before trying to grab it
int min_tolerance = 10;	// smallest size blob that is a pom-pom
int srv_tolerance = 20;	// range that servo must be between from it's desired position before returning

// Color id's
int pink = 0;
int green = 1;
//int black = 2;

// Coordinates for the grabing range bounding box
int leftb = 70;
int rightb = 90;
int topb = 25;
int bottomb = 45;
int cam_x = 45;
int cam_y = 20;

// Motor id's
int r_motor = 3;
int l_motor = 0;
int bucket_motor = 2;
int arm_srv = 2;
int claw_srv = 0;

// important positions
int bucket_down = 0;
int bucket_up = 3000;
int arm_down = 1910;
int arm_at_camera = 1550;
int arm_green = 644;
int arm_pink = 480;
int claw_opened = 1950;  
int claw_closed = 10;

// ### Don't define these, altered in code ###
int c_color; // Color of the blob to be picked up
int c_blob;  // id of the blob to be picked up
int center_x;// Calculated center claw x coordinate
int center_y;
// ### Don't define these, altered in code ###

//determines weather or not the seen blobs qualify as pom-poms
//in:	void
//out:	true if blobs are pom-poms 
//		false if not
int any_balls()
{
	int i;
	for (i = 0;i<track_count(pink);i++)//loop for pink blobs
	{
		// If it is bigger than tolerance return 1
		if (track_size(pink,i) > min_tolerance)
		{
			return 1;
		}
	}
	for (i = 0;i<track_count(green);i++)//loop for green blobs
	{
		// If it is bigger than tolerance return 1
		if (track_size(green,i) > min_tolerance)
		{
			return 1;
		}
	}

	// No blobs meet qualifications, return 0
	return 0;
}

//custom track update, currently only sleeps pror to track_updating
//in:	void
//out:	void
void track_update_c()
{
	msleep(5);
	track_update();
}

//temporary function to wait for pom-poms to be placed nearby
//in:	void
//out:	void
void wait()
{
	do
	{
		track_update_c();
	}while(! any_balls());
}

//finds the x component of the distance of a point to the center point
//in:	int x - x-coordinate of a point
//out:	the distance as a positive int
int xdist(int x)
{
	int dif = center_x - x;
	return(sqrt(dif*dif));
}

//finds the y component of the distance of a point to the center point
//in:	int y - y-coordinate of a point
//out:	the distance as a positive int
int ydist(int y)
{
	int dif = center_y - y;
	return(sqrt(dif*dif));
}

//custom track_x used to ensure a usable value is returned for track_x
//it is mostly the same as track_x
//in:	int color - color of the blob
//		int i - id of the blob
//out:	x-coordinate of the blob given if it is in range of track
//		if not it returns -200, which is an out-of-the way variable
int track_xc(int color,int i)
{
	return((i<track_count(color))?track_x(color,i):-256);
}

//custom track_y used to ensure a usable value is returned for track_y
//it is mostly the same as track_y
//in:	int color - color of the blob
//		int i - id of the blob
//out:	x-coordinate of the blob given if it is in range of track
//		if not it returns -200, which is an out-of-the way variable
int track_yc(int color,int i)
{
	return((i<track_count(color))?track_y(color,i):-256);
}

//finds the distance of a blob from the center point
//in:	blob values color and i
//out:	the distance
int dist(int color,int i)
{
	int x = track_xc(color,i) - center_x;
	int y = track_yc(color,i) - center_y;
	return sqrt(x*x + y*y);//pythagorean theorem (lol)
}

//establish closest blob & color into global vars c_blob and c_color
//in:	void
//out:	void
void update_closest()
{
	track_update_c();
	c_blob=0;
	c_color=pink;
	int i;
	//these loops check the distance of a pom-pom and compare it to the old closest
	for (i = 0;i<track_count(pink);i++)//pink loop
	{
		if (dist(pink,i)<dist(c_color,c_blob) && track_size(pink,i) > min_tolerance)
		{
			c_blob=i;
			c_color=pink;
		}
	}
	for (i = 0;i<track_count(green);i++)//green loop
	{
		if (dist(green,i)<dist(c_color,c_blob) && track_size(green,i) > min_tolerance)
		{
			c_blob=i;
			c_color=green;
		}
	}
}

//moves the motors to center the pom-poms
//in:	void
//out:	void
void center_ball()
{
	int rot;
	do
	{
		//moving x within range
		do
		{
			update_closest();
			rot = (center_x - track_xc(c_color,c_blob)) * 10;
			//printf("r:%d  ",rot);
			//printf("l:%d\n",-rot);
			mav(r_motor,rot);
			mav(l_motor,-rot);
		}while(xdist(track_xc(c_color,c_blob))>range && any_balls());
		//moving y within range
		do
		{
			update_closest();
			rot = (center_y - track_yc(c_color,c_blob)) * 10;
			//printf("r:%d  ",rot);
			//printf("l:%d\n",rot);
			mav(r_motor,rot);
			mav(l_motor,rot);
		}while(ydist(track_yc(c_color,c_blob))>range && any_balls());
	}while(xdist(track_xc(c_color,c_blob))>range && ydist(track_yc(c_color,c_blob))>range && any_balls());
	ao();
}

//determines the color of the largest blob on camera
//in:	void
//out:	color of the blob as int
int get_color()
{
	int i;
	int big_size = 0;
	int color = 0;
	for(i=0;i<track_count(pink);i++){
		if(track_size(pink,i)>big_size){
			big_size=track_size(pink,i);
			color = pink;
		}
	}
	for(i=0;i<track_count(green);i++){
		if(track_size(green,i)>big_size){
			big_size=track_size(green,i);
			color = green;
		}
	}
	return(color);
}

//this code is untested
int color_in_hand()
{
	int radius , xc , yc , x , y , dist , color , i;
	int mindist = 100000;
	for(i=0;i<track_count(pink);i++){
		radius = track_bbox_width(pink,i) / 2;
		x = track_x(pink,i);
		y = track_y(pink,i);
		if (x < cam_x)
			xc = x + radius;
		if (x > cam_x)
			xc = x - radius;
		if (y < cam_y)
			yc = y + radius;
		if (y > cam_y)
			yc = y - radius;
		if (x + radius > cam_x && x - radius < cam_x)
			xc = cam_x;
		if (y + radius > cam_y && y - radius < cam_y)
			yc = cam_y;
		dist = (cam_x - xc)*(cam_x - xc) + (cam_y - yc)*(cam_y - yc);
		if (dist < mindist)
		{
			mindist = dist;
			color = pink;
		}
		if ( dist == 0)
			return pink;
	}
	for(i=0;i<track_count(green);i++){
		radius = track_bbox_width(green,i) / 2;
		x = track_x(green,i);
		y = track_y(green,i);
		if (x < cam_x)
			xc = x + radius;
		if (x > cam_x)
			xc = x - radius;
		if (y < cam_y)
			yc = y + radius;
		if (y > cam_y)
			yc = y - radius;
		if (x + radius > cam_x && x - radius < cam_x)
			xc = cam_x;
		if (y + radius > cam_y && y - radius < cam_y)
			yc = cam_y;
		dist = (cam_x - xc)*(cam_x - xc) + (cam_y - yc)*(cam_y - yc);
		if (dist < mindist)
		{
			mindist = dist;
			color = green;
		}
		if ( dist == 0)
			return green;
	}
	return color;
}

//moves the servo to the position but waits for it to get there first
//in: 	srv
//out:	void
void srv_pos(int srv, int pos)
{
	int direction = ((pos - get_servo_position(srv) > 0) * 2 - 1) * srv_tolerance;
	while(get_servo_position(srv) < pos - srv_tolerance || get_servo_position(srv) > pos + srv_tolerance)
	{
		set_servo_position(srv, get_servo_position(srv) + direction);
	}
	set_servo_position(srv, pos);
}

//this code doesnt work
/*int black_blob()
{
	int i;
	int blob;
	int size = 0;
	track_update_c();
	for (i = 0; i < track_count(black); i++)
	{
		if (track_size(black, i) > size)
		{
			blob = i;
			size = track_size(black, i);
		}
	}
	return blob;
}

void align(int desired_position,int tolerance)
{
	int angle , i , speed , position;
	do
	{
		i = black_blob();
		angle = track_angle(black, i);
		speed = angle * 100;
		mav(r_motor, -speed);
		mav(l_motor, speed);
	}while (angle != 0);	// i have SERIOUS doubts as to the accuracy of track_angle (its an int that expresses radians!?)
	do
	{
		i = black_blob();
		position = track_bbox_bottom(black,i);
		speed = (position - desired_position) * 100.0 / 12.0;
		mav(r_motor, -speed);	// is it + or - ? reversed y axis?
		mav(l_motor, -speed);
	}while (position - desired_position > tolerance || desired_position - position > tolerance);
	ao();
}*/

//main function currently centers a pom-pom, but will do more eventually
//in:	void
//out: 	for some reason; an int
int main()
{
	float start_time = seconds();
	
	center_x = (rightb + leftb)/2;
	center_y = (topb + bottomb)/2;
	// placing these here fixes an error at initialization
	
	wait();	// instead here i need to place code that sets up the robot to see the pom poms from its start probably like turn 90deg
	
	set_servo_position(claw_srv,claw_opened);
	track_update_c();
	while(seconds() - start_time < 60 && any_balls())	// objective 60 second limit
	{
		center_ball();//camera & wheels
		printf("1\n");
		srv_pos(arm_srv,arm_down);//arm to ground
		msleep(5);
		printf("2\n");
		set_servo_position(claw_srv,claw_closed);//claw movement
		printf("3\n");
		srv_pos(arm_srv,arm_at_camera);//lift arm
		printf("4\n");
		if (color_in_hand() == pink)
		{
			srv_pos(arm_srv,arm_pink);
		}
		else
		{
			srv_pos(arm_srv,arm_green);
		}
		printf("5\n");
		msleep(5);
		set_servo_position(claw_srv,claw_opened);//open claw
		track_update_c();
	}
	// here i need code to figure out where i am, and drive and align with the buckets
	clear_motor_position_counter(bucket_motor);
	mtp(bucket_motor,1000,bucket_up);
	get_motor_done(bucket_motor);
	mtp(bucket_motor,1000,bucket_down);
	//here maybe go back to starting position and loop until time is up
}
