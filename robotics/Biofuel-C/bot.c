/***************************************************************************
*********  This comment area should be filled with a description  
*********         of your program and what it does.               
***************************************************************************/
/* Libraries used in Botball are automatically included, but list any additional includes below */
/* #defines and constants go below.*/
#define LEFT_MOTOR 0
#define RIGHT_MOTOR 3

#define TOPHAT_PORT 1

#define LOGIC_DEBUG 0

//#define ever ;;
/* Global variables go below (if you absolutely need them).*/

/*Function prototypes below*/
int servo = 0;
int rate, trvl_dstnc;

void ease_servo(int servo, int new_pos, float time_frame) //should I be using a time frame?
{
	int start_pos, delta_pos, y;
	float start_time, time;
	
	enable_servos();
	start_time = seconds();
	start_pos = get_servo_position(servo);
	delta_pos = new_pos - start_pos;
	moving = 1;
	
	do {
		time = seconds() - start_time;
		// TODO: Overtime check
		if (time > start_time) {
			set_servo_position(servo, (start_pos + delta_pos));
			moving = 0;
			break;
		}
		
		y = (int)(delta_pos * time / time_frame + start_pos);
		
		if((delta_pos > 0 && y > start_pos + delta_pos) ||
		   (delta_pos < 0 && y < start_pos + delta_pos)) {
			y = start_pos + delta_pos;
			moving = 0;
		}
		
		set_servo_position(servo, y);
		sleep(0.05);
	} while(moving);
}

void my_mav(int motor, float velocity)
{
	if (!LOGIC_DEBUG)
	{
		mav(motor, velocity);
	}
}

void wmr_move(int vel)
{
	my_mav(LEFT_MOTOR, vel);
	my_mav(RIGHT_MOTOR, vel);
}

void wmr_movet(int vel, float time)
{
	wmr_move(vel);
	
	sleep(time);
}

void wmr_turn_left()
{
	my_mav(LEFT_MOTOR, -250);
	my_mav(RIGHT_MOTOR, 250);
	
	sleep(3);
}

void wmr_turn_right()
{
	my_mav(LEFT_MOTOR, 250);
	my_mav(RIGHT_MOTOR, -250);

	sleep(3);
}

int main()///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
{
	int tophat, tries, lmotor, rmotor, speed, downtime;
	float until, stepSize;
	
	set_servo_position(0,200); // get ready to catch balls
	mav(0,-1000);//drive to catch the balls
	mav(3,-950);
	sleep(2.6);
    
	ao();//stop in front of the ball drop (tube of ping-pong balls)
    sleep(3);
	
	mav(0,-640);//moves up to the black line
	mav(3,-640);
	sleep(3.5);
	
	mav(0,-500);//turns around to start following the line
    mav(3,500);
	sleep(1.3);
	
	mav(3,500);//drive into the black line
	mav(0,500);
	sleep(.75);
	
	while(digital(8) == 0)//for (ever)
	{
		tophat = analog(TOPHAT_PORT);
		
		if (tophat >= 190)
		{
			printf("On the line...\n");
			wmr_move(250);
			
			msleep(250); // FIX THIS LATER BRO
		}
		else
		{
			printf("WHERE'S THE LINE AT????\n");
			
			tries = 0;
			stepSize = 0.0;
			lmotor = 50;
			rmotor = -50;
			speed = -250;
			
			do {
				if (lmotor > 0)
				{
					lmotor = speed;
					rmotor = abs(speed);
				}
				else
				{
					lmotor = abs(speed);
					rmotor = speed;
				}
				
				my_mav(LEFT_MOTOR, lmotor);
				my_mav(RIGHT_MOTOR, rmotor);
				
				stepSize += 0.5;
				until = seconds() + stepSize;
				
				do {
					tophat = analog(TOPHAT_PORT);
					printf("Searching... until: %f Seconds: %f\n", until, seconds());
				} 
				while (tophat <= 190 && seconds() <= until);
				
				tries++;
				printf("Try: %d\n", tries);
			} 
			while(tophat < 190 && tries <= 15);
			
			if (tries >= 15)
			{
				beep();
				printf("Could not find the line\n");
				return;
			}
			
		}
	}
	downtime = .24;
	int servopot = 550;
	while(servpot != 1600)
	{
	servpot + 40;
	set_servo_position(0,servpot);
	sleep(downtime);
	}
		
}


/*Function definitions go below.*/
