// ##### MOVEMENT #####

void move(int vel);
void movet(int vel, float time);
void rotate(enum Turn_Direction direction, int speed);
void turn(enum Turn_Direction direction, float time);

// ##### TARGETING #####

void target_center();
int target_inclaw();

// ##### CLAW CONTROL #####

void claw(enum Claw_State state);
void claw_set(enum Claw_State state);
void claw_winch(enum Winch_State state);
void _wS(enum Winch_Speed speed, enum Winch_State state);

// ##### GENERAL #####

void reset_motors();
void out(const char * Str);
void dbg(const char * Str);
void blob_debug();

// ##### Main #####

void setup();
//void main();
