// General configuration defines
#define REAL_MODE 0
#define _DEBUG 1 // Remove to disable

// Motor ports
#define LEFT_MOTOR 0
#define RIGHT_MOTOR 3
#define WINCH_MOTOR 2

// Servo port
#define CLAW_SERVO 0

// Touch sensor port
#define CLAW_SENSOR 8

#define MIN_BLOB 900

// Just for turn()
enum Turn_Direction
{
	LEFT, RIGHT
};
#define TURN_SPEED -250

// These defines control the targeting system
#define CENTER_X 60
#define CENTER_TOLERANCE 4

// Claw states
enum Claw_State
{
	UNKOWN = -1,
	CLOSED,
	CLOSED_HOVER,
	CLOSED_TOP,
	OPEN,
	OPEN_HOVER,
	OPEN_TOP
};

// Claw positions
#define CLAW_SERVO_CLOSED 1200
#define CLAW_SERVO_OPEN 0
// -
#define CLAW_WINCH_BOTTOM 0
#define CLAW_WINCH_HOVER 2000
#define CLAW_WINCH_TOP 15000
#define CLAW_WINCH_STACK 14100

// Winch speeds
#define WINCH_SPEED_NORMAL 500
#define WINCH_SPEED_PLACE 250

// Winch states
enum Winch_State 
{
	BOTTOM = CLAW_WINCH_BOTTOM,
	HOVER = CLAW_WINCH_HOVER,
	TOP = CLAW_WINCH_TOP,
	STACK = CLAW_WINCH_STACK
};
enum Winch_Speed
{
	NORMAL = WINCH_SPEED_NORMAL,
	PLACE = WINCH_SPEED_PLACE
};

// heh
#define ever ;;

// Include headers after defines and enums
#include "blockBot.h"

// State tracking variables
enum Claw_State _curClawState = UNKOWN;
enum Winch_State _curWinchState = BOTTOM;

#pragma region Movement

// ##### MOVEMENT #####

void move(int vel)
{
	move_at_velocity(LEFT_MOTOR, vel);
	move_at_velocity(RIGHT_MOTOR, vel);
}

/// <summary>
/// Moves at a rate for a time
/// </summary>
void movet(int vel, float time)
{
	move(vel);

	sleep(time);

	move(0);
}

void rotate(enum Turn_Direction direction, int speed)
{
	int r_speed, l_speed;

	if (speed > 0)
	{
		speed = speed * -1;
	}

	r_speed = (direction == RIGHT) ? abs(speed) : speed;
	l_speed = (direction == LEFT)  ? abs(speed) : speed;

	move_at_velocity(RIGHT_MOTOR, r_speed);
	move_at_velocity(LEFT_MOTOR, l_speed);
}

void turn(enum Turn_Direction direction, float time)
{
	rotate(direction, TURN_SPEED);

	sleep(time);

	move(0);
}

#pragma endregion

#pragma region Camera Targetting

// ##### TARGETING #####

void target_center()
{
	/*if (!track_is_new_data_available())
	{
		return;
	}*/
	
	int x, diff, speed, run, sleep;
	enum Turn_Direction direction;

	track_update();
	x = track_x(0,0);

	out("Centering");

	while (x > CENTER_X + CENTER_TOLERANCE || x < CENTER_X - CENTER_TOLERANCE)
	{
		// We need to position ourselfs
		printf(".");

		direction = (x < CENTER_X) ? LEFT : RIGHT;
		diff = abs(x - CENTER_X);

		speed = (diff > 75) ? 100 : ((diff < 25) ? 50 : 25);
		run   = (diff > 75) ? 100 : 150;
		sleep = (diff > 75) ? 250 : 100;

		// Rotate with x for y ms and sleep for z ms
		rotate(direction, speed);
		msleep(run);
		move(0);
		msleep(sleep);

		track_update();
		x = track_x(0,0);
		printf("%i", x);
	}

	move(0);
}

int target_inclaw()
{
	return (digital(CLAW_SENSOR));
}

#pragma endregion

#pragma region Claw Control

// ##### CLAW CONTROL #####

void claw(enum Claw_State state)
{
	switch (state)
	{
	case OPEN:
	case CLOSED:
		claw_set(state);
		break;

	case CLOSED_HOVER:
		// It won't hurt to repeat this again
		claw_set(state);
		msleep(500);
	case OPEN_HOVER:
		claw_winch(HOVER);
		claw_set(state);
	}

	_curClawState = state;
}

void claw_set(enum Claw_State state)
{
	if (state == OPEN || state == OPEN_HOVER || state == OPEN_TOP)
	{
		set_servo_position(CLAW_SERVO, CLAW_SERVO_OPEN);
	}
	else
	{
		set_servo_position(CLAW_SERVO, CLAW_SERVO_CLOSED);
	}
}

void claw_winch(enum Winch_State state)
{
	if (state == _curWinchState)
	{
		return;
	}
	
	switch (state)
	{
	case BOTTOM:
	case HOVER:
		_wS(NORMAL, state);
		break;
	case STACK:
	case TOP:
		_wS(NORMAL, TOP);
	}

	if (state == STACK)
	{
		_wS(PLACE, STACK);
	}

	_curWinchState = state;
}

void _wS(enum Winch_Speed speed, enum Winch_State state)
{
	move_to_position(WINCH_MOTOR, speed, state);
	// TODO: This isn't working right....
	get_motor_done(WINCH_MOTOR);
}

#pragma endregion

#pragma region General

// ##### GENERAL #####

void reset_motors();
void out(const char * Str);
void dbg(const char * Str);
void blob_debug();

void reset_motors()
{
	clear_motor_position_counter(WINCH_MOTOR);
	clear_motor_position_counter( LEFT_MOTOR);
	clear_motor_position_counter(RIGHT_MOTOR);
}

void out(const char * Str)
{
	printf("%s\n", Str);
}

void dbg(const char * Str)
{
#ifdef _DEBUG
	out(Str);
#endif
}

void blob_debug()
{
	int x, y, i;

	for (i = 0; i < (track_count(0) - 1); i++)
	{
		x = track_x(0, i);
		y = track_y(0, i);

		printf("B%i : Found something at %i x and %i y\n", i, x, y);
		printf("B%i : Size is %i\n", i, track_size(0, i));
	}
}

#pragma endregion

/// <summary>Motor and servo setup</summary>
void setup()
{
	// Reset position to zero on the motors
	reset_motors();
	
	// We have very open arms (get it? heh)
	claw(OPEN);

	// Enable servos AFTER you set the position
	enable_servos();
}

//////////////////////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////////////////

/// <summary> Main program entry-point</summary>
int main()
{
	out("*** Please make sure we're facing the blocks and the winch line isn't loose ***");
	
	// TODO: Calibrate light sensor here

	setup();
	
	// TODO: Wait...some more

	// Move forward a tiny bit
	//movet(750, 3); HACK
	
	// Turn more to the right of the southern block
	//turn(RIGHT,  0.8); HACK

	out("Looking for the block...");

	// Sweep left
	//rotate(LEFT, 150);

	// Keep going left until we find a big enough blob
	do
	{
		printf(".");
		turn(LEFT, 0.2); // FIXME: This is compensation for my home lighting
		sleep(1.5);
		//msleep(25);
		track_update();
	} while (track_size(0, 0) < MIN_BLOB);

	out("We've located a blob big enough (whoop whoop)");

	out("Targeting");

	float start = seconds();
	target_center();

	//while(start > seconds() - 4)
	while (!target_inclaw())
	{
		target_center();
		move(250);
		msleep(250);
	}

	out("We're in the claw");

	// Stop moving
	move(0);

	// Grab it!
	claw(CLOSED_HOVER);
	
	// Back up...
	movet(-500, 1);
	
	for (ever){}

	out("WHERES THE OTHER BLOCK I NEED THAT BLOCK");

	

	out("That's all for now folks...");

	blob_debug();
	
	return 0;
}

