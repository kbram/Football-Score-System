# Real-Time Football Score System with WebSockets

A comprehensive real-time football score tracking system built with **Laravel 10**, **Laravel Reverb** (WebSockets), and modern web technologies. This system demonstrates live score updates, match management, automatic timer functionality, and role-based access control with real-time broadcasting capabilities.

## 🚀 Features

### Core Functionality

-   ⚽ **Real-time Score Updates**: Live score broadcasting via WebSockets
-   📊 **Match Management**: Create, update, and manage football matches
-   🎮 **Control Panel**: Admin interface for real-time match control
-   ⏱️ **Automatic Timer System**: Match timers run automatically with live updates
-   🔐 **Role-Based Access Control**: Admin vs Client user permissions
-   📱 **Responsive Design**: Works on desktop, tablet, and mobile devices
-   🔄 **Live Synchronization**: Multiple clients sync automatically
-   👥 **User Registration**: Automatic role assignment for new users

### Technical Features

-   🔌 **WebSocket Integration**: Laravel Reverb for real-time communication
-   📡 **Event Broadcasting**: Pusher-compatible real-time events
-   ⚙️ **Scheduled Commands**: Automatic timer updates every minute
-   🗄️ **Repository Pattern**: Clean, maintainable code architecture
-   ✅ **Form Validation**: Comprehensive request validation
-   🔒 **Middleware Protection**: Controller-level security for admin functions
-   📋 **DataTables**: Ajax-powered data tables with search and pagination
-   🎨 **Modern UI**: Tailwind CSS responsive design

## 🏗️ Architecture Overview

```
┌─────────────────┐    ┌─────────────────┐    ┌─────────────────┐
│   Frontend      │    │   Backend       │    │   WebSocket     │
│   (Blade/JS)    │◄──►│   (Laravel)     │◄──►│   (Reverb)      │
└─────────────────┘    └─────────────────┘    └─────────────────┘
         │                       │                       │
         └───────────────────────┼───────────────────────┘
                                 │
                    ┌─────────────────┐
                    │    Database     │
                    │    (MySQL)      │
                    └─────────────────┘
```

## 📦 Installation & Setup

### Prerequisites

-   PHP 8.1+
-   Composer
-   Node.js & NPM
-   MySQL/PostgreSQL

### 1. Clone & Install Dependencies

```bash
# Clone the repository
git clone <repository-url>
cd Football-Score-System

# Install PHP dependencies
composer install

# Install JavaScript dependencies
npm install

# Copy environment file
cp .env.example .env
```

### 2. Configure Environment

Update your `.env` file with the following settings:

```env
# Database Configuration
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=football_scores
DB_USERNAME=your_username
DB_PASSWORD=your_password

# Broadcasting Configuration
BROADCAST_DRIVER=reverb

# Reverb WebSocket Configuration
REVERB_APP_ID=football-score-app
REVERB_APP_KEY=local-key
REVERB_APP_SECRET=local-secret
REVERB_HOST=localhost
REVERB_PORT=8080
REVERB_SCHEME=http

# Pusher Configuration (for frontend)
PUSHER_APP_ID="${REVERB_APP_ID}"
PUSHER_APP_KEY="${REVERB_APP_KEY}"
PUSHER_APP_SECRET="${REVERB_APP_SECRET}"
PUSHER_HOST="${REVERB_HOST}"
PUSHER_PORT="${REVERB_PORT}"
PUSHER_SCHEME="${REVERB_SCHEME}"
PUSHER_APP_CLUSTER=mt1
```

### 3. Database Setup

```bash
# Generate application key
php artisan key:generate

# Run migrations (includes roles and users tables)
php artisan migrate

# Seed roles and admin user
php artisan db:seed --class=DatabaseSeeder

# Seed demo data (optional)
php artisan db:seed --class=FootballMatchSeeder
```

### 4. Start Timer System

```bash
# Add to crontab for automatic timer updates
* * * * * cd /path-to-your-project && php artisan schedule:run >> /dev/null 2>&1

# Or manually test the timer command
php artisan matches:update-timers
```

### 4. Build Assets

```bash
# Build frontend assets
npm run build

# Or for development with hot reload
npm run dev
```

## 👥 User Roles & Access Control

### Administrator Role

-   **Full Access**: Create, edit, delete matches
-   **Control Panel**: Real-time match controls and timer management
-   **User Management**: Admin privileges for all functions
-   **Auto-Login Redirect**: Redirected to `/football/matches` after login

### Client Role

-   **View Only**: Can view matches and live scores
-   **No Admin Controls**: Cannot access control panels or create matches
-   **Registration Default**: New users automatically assigned Client role
-   **Auto-Login Redirect**: Redirected to `/football/matches` after login

### Account Setup

```bash
# Default admin user (created by seeder):
# Email: admin@admin.com
# Password: password
# Role: Administrator

# New registrations automatically get Client role
# Visit /register to create new client accounts
```

## 🎯 Running the Application

### Method 1: Laravel Development Server

```bash
# Terminal 1 - Start Laravel development server
php artisan serve

# Terminal 2 - Start Reverb WebSocket server
php artisan reverb:start

# Terminal 3 - Start Vite development server (for hot reload)
npm run dev

# Terminal 4 - Start the automatic timer system (optional for manual testing)
php artisan matches:update-timers
```

### Method 2: Using Laravel Sail (Docker)

```bash
# Start all services
./vendor/bin/sail up -d

# Start Reverb WebSocket server
./vendor/bin/sail artisan reverb:start
```

## 🌐 Application URLs

Once running, access the application at:

-   **Main Application**: http://localhost:8000 (redirects to football matches after login)
-   **Football Matches**: http://localhost:8000/football/matches (default landing page)
-   **Live Scores**: http://localhost:8000/football/live-scores
-   **User Registration**: http://localhost:8000/register (auto-assigns Client role)
-   **Login**: http://localhost:8000/login (redirects to football matches)
-   **Standalone Client**: http://localhost:8000/live-client.html

## 📱 Usage Guide

### For Administrators

1. **Login as Admin**:

    - Email: `admin@admin.com`
    - Password: `password`
    - Automatically redirected to `/football/matches`

2. **Create a Match**:

    - Go to `/football/matches`
    - Click "Create New Match" (only visible to admins)
    - Fill in team names and details

3. **Control Panel**:

    - Access via "Control Panel" button on any match
    - Update scores in real-time
    - Change match status (Not Started → In Progress → Half Time → Finished)
    - Update match time manually or let automatic timer run
    - Start/stop automatic timer system

4. **Monitor Live Updates**:
    - Use "Live Scores" to view real-time updates
    - Open multiple browser tabs to see synchronization
    - Timer updates broadcast every minute automatically

### For Client Users

1. **Register New Account**:

    - Visit `/register` to create new account
    - Automatically assigned "Client" role
    - Redirected to football matches after registration

2. **View-Only Access**:

    - Can view all matches and scores
    - Cannot access admin controls (hidden in UI)
    - Control panels show "View Only Mode" message
    - No "Create New Match" buttons visible

3. **Watch Live Scores**:
    - Visit `/football/live-scores` for all matches
    - Visit `/football/live-scores/{id}` for specific match
    - Real-time updates without page refresh
    - Connection status indicator

## 🔧 API Endpoints

### Match Management

```http
GET    /football/matches                    # List all matches (all users)
POST   /football/matches                    # Create new match (admin only)
GET    /football/matches/{id}               # Show specific match (all users)
PUT    /football/matches/{id}               # Update match (admin only)
DELETE /football/matches/{id}               # Delete match (admin only)

# Real-time Controls (Admin Only)
POST   /football/matches/{id}/update-score  # Update match score
POST   /football/matches/{id}/update-status # Update match status
POST   /football/matches/{id}/update-time   # Update match time
POST   /football/matches/{id}/simulate-goal # Simulate a goal

# Live Data (All Users)
GET    /football/matches/{id}/live-data     # Get current match data
GET    /football/matches-data               # DataTables Ajax endpoint
GET    /football/matches/{id}/control-panel # Admin control panel (admin only)

# Timer System
GET    /football/matches/{id}/live          # Live score view (all users)
```

### Authentication & Authorization

```http
GET    /register                           # User registration form
POST   /register                           # Create new user (auto-assigns Client role)
GET    /login                              # Login form
POST   /login                              # Authenticate user (redirects to /football/matches)
POST   /logout                             # Logout user

# Role-based middleware protection on admin routes
# 403 error returned for non-admin users attempting admin actions
```

### WebSocket Channels

```javascript
// Global channel for all matches
channel: "football-matches";

// Match-specific channel for real-time updates
channel: "football-match.{matchId}";

// Event names
event: "score.updated";           // Score changes, status updates
event: "timer.updated";           // Automatic timer updates (every minute)

// Event data structure
{
    match: {
        id: 1,
        team_a_score: 2,
        team_b_score: 1,
        status: "in_progress",
        current_match_time: 67,
        timer_running: true,
        // ... other match data
    },
    event_type: "score_update" | "timer_update" | "status_change",
    event_data: {
        message: "Goal scored!",
        // ... additional event data
    },
    timestamp: "2025-08-08T10:30:00Z"
}
```

## 🎮 Testing Real-time Features

### 1. Role-Based Access Testing

**Admin User Testing:**

1. Login as admin (`admin@admin.com` / `password`)
2. Verify "Create New Match" button is visible
3. Access control panel for any match
4. Test real-time score updates and timer controls

**Client User Testing:**

1. Register new user account (gets Client role automatically)
2. Login and verify no admin buttons visible
3. Try accessing control panel - should show "View Only Mode"
4. Verify live score viewing works without admin controls

### 2. Timer System Testing

1. Create a match and set status to "In Progress"
2. Watch automatic timer updates every minute
3. Open multiple browser tabs to see synchronized timer
4. Test manual timer override in control panel
5. Verify timer stops when status changes to "Half Time" or "Finished"

### 3. Manual Real-time Testing

1. Open the Control Panel in one browser tab (as admin)
2. Open Live Score view in another tab
3. Update scores/status in Control Panel
4. Watch real-time updates in Live Score view
5. Test with multiple users logged in simultaneously

### 2. Using the Demo Client

Visit `http://localhost:8000/live-client.html` for a standalone client with demo controls:

-   **Simulate Goal**: Randomly adds goals to matches
-   **Change Status**: Updates match status
-   **Update Time**: Changes match time

### 3. Multiple Browser Testing

-   Open multiple browser windows/tabs
-   Make changes in one window
-   Observe real-time synchronization across all windows

## 🗂️ Project Structure

```
app/
├── Console/
│   ├── Kernel.php                         # Scheduled commands
│   └── Commands/
│       └── UpdateMatchTimers.php          # Automatic timer updates
├── Events/
│   └── ScoreUpdated.php                   # WebSocket event
├── Http/
│   ├── Controllers/
│   │   ├── Auth/
│   │   │   ├── AuthenticatedSessionController.php  # Login redirect
│   │   │   └── RegisteredUserController.php        # Auto role assignment
│   │   └── WEB/
│   │       └── Football/
│   │           └── FootballMatchController.php     # Role-based middleware
│   └── Requests/
│       └── FootballMatchRequest.php       # Form validation
├── Models/
│   ├── FootballMatch.php                 # Match model with timer logic
│   ├── User.php                          # User model with role relationships
│   └── Role.php                          # Role model (Admin/Client)
├── Providers/
│   └── RouteServiceProvider.php          # Login redirect configuration
└── Repositories/
    └── Football/
        └── FootballMatchRepository.php   # Business logic

resources/
├── views/
│   └── football/
│       └── matches/
│           ├── index.blade.php        # Match listing
│           ├── live.blade.php         # Live score view
│           ├── control-panel.blade.php # Admin controls
│           └── create.blade.php       # Create match form
└── js/
    └── app.js                         # Frontend JavaScript

database/
├── migrations/
│   ├── 2013_10_01_000001_create_roles_table.php      # User roles
│   ├── 2014_10_12_000000_create_users_table.php      # Users with role_id
│   ├── create_football_matches_table.php             # Match data
│   └── add_started_at_to_football_matches_table.php  # Timer fields
└── seeders/
    ├── DatabaseSeeder.php                # Calls all seeders
    ├── AdminUserSeeder.php               # Creates admin user
    └── FootballMatchSeeder.php           # Demo data

public/
└── live-client.html                       # Standalone client
```

## 🔧 Customization

### Adding New User Roles

1. **Create New Role**:

```php
// In database seeder or tinker
Role::create([
    'name' => 'Moderator',
    'slug' => 'moderator',
    'description' => 'Can moderate but not create'
]);
```

2. **Add Role Methods to User Model**:

```php
public function isModerator(): bool
{
    return $this->role && $this->role->slug === 'moderator';
}
```

3. **Update Middleware and Views**:

```php
// Add to controller middleware checks
if (!auth()->user()->isAdmin() && !auth()->user()->isModerator()) {
    abort(403);
}
```

### Customizing Timer Behavior

1. **Modify Timer Update Frequency**:

```php
// In app/Console/Kernel.php
$schedule->command('matches:update-timers')->everyFiveMinutes(); // Change frequency
```

2. **Custom Timer Logic**:

```php
// In UpdateMatchTimers command
if ($match->status === 'in_progress' && $match->timer_running) {
    // Add custom timer calculations
    $newTime = $match->current_match_time + $customIncrement;
}
```

### Adding New Event Types

1. **Update the Event**:

```php
// In ScoreUpdated.php
public function broadcastWith(): array
{
    return [
        'match' => $this->match,
        'event_type' => 'your_custom_event',
        'event_data' => ['custom' => 'data']
    ];
}
```

2. **Handle in Frontend**:

```javascript
channel.bind("score.updated", function (data) {
    if (data.event_type === "your_custom_event") {
        // Handle custom event
    }
});
```

### Role-Based UI Customization

1. **Add Role Checks to Views**:

```blade
@if(auth()->user()->isAdmin())
    <!-- Admin-only content -->
@elseif(auth()->user()->isClient())
    <!-- Client-specific content -->
@endif
```

2. **JavaScript Role Handling**:

```javascript
const isAdmin = {{ auth()->user()->isAdmin() ? 'true' : 'false' }};
if (isAdmin) {
    // Enable admin features
}
```

### Custom Styling

The application uses Tailwind CSS. Customize by:

1. Modifying `tailwind.config.js`
2. Adding custom CSS in `resources/css/app.css`
3. Updating Blade templates

## 🧪 Testing

### Run Tests

```bash
# Run all tests
php artisan test

# Run specific test file
php artisan test tests/Feature/FootballMatchTest.php

# Run with coverage
php artisan test --coverage
```

### WebSocket Testing

```bash
# Test WebSocket connection
php artisan reverb:ping

# Monitor WebSocket traffic
php artisan reverb:status
```

## 🚀 Deployment

### Production Configuration

1. **Environment**:

```env
APP_ENV=production
APP_DEBUG=false
BROADCAST_DRIVER=reverb

# Use secure WebSocket
REVERB_SCHEME=https
REVERB_PORT=443

# Database for production
DB_HOST=your-production-db-host
DB_DATABASE=your-production-db-name
```

2. **Cron Job for Timer System**:

```bash
# Add to server crontab
* * * * * cd /path-to-your-project && php artisan schedule:run >> /dev/null 2>&1
```

3. **WebSocket Server with Supervisor**:

```ini
[program:reverb]
process_name=%(program_name)s_%(process_num)02d
command=php /path-to-your-project/artisan reverb:start --host=0.0.0.0 --port=8080
autostart=true
autorestart=true
user=www-data
numprocs=1
redirect_stderr=true
stdout_logfile=/path-to-your-project/storage/logs/reverb.log
```

4. **Reverse Proxy** (Nginx):

```nginx
# WebSocket proxy
location /app/ {
    proxy_pass http://localhost:8080;
    proxy_http_version 1.1;
    proxy_set_header Upgrade $http_upgrade;
    proxy_set_header Connection "upgrade";
    proxy_set_header Host $host;
    proxy_cache_bypass $http_upgrade;
}

# Application
location / {
    try_files $uri $uri/ /index.php?$query_string;
}
```

## 🤝 Contributing

1. Fork the repository
2. Create a feature branch: `git checkout -b feature/new-feature`
3. Make changes and commit: `git commit -am 'Add new feature'`
4. Push to branch: `git push origin feature/new-feature`
5. Submit a Pull Request

## 📝 License

This project is licensed under the MIT License. See the [LICENSE](LICENSE) file for details.

## 🆘 Troubleshooting

### Common Issues

1. **WebSocket Connection Failed**:

    - Check if Reverb server is running: `php artisan reverb:start`
    - Verify port 8080 is not blocked
    - Check browser console for connection errors

2. **Broadcasting Not Working**:

    - Ensure `BROADCAST_DRIVER=reverb` in `.env`
    - Check Reverb configuration
    - Verify event is implementing `ShouldBroadcast`

3. **Database Connection Issues**:

    - Verify database credentials in `.env`
    - Ensure database exists
    - Run `php artisan migrate` if tables are missing
    - Check if roles table exists: `SHOW TABLES LIKE 'roles'`

4. **Timer System Issues**:

    - Verify cron job is running: `crontab -l`
    - Test timer command manually: `php artisan matches:update-timers`
    - Check Laravel scheduler: `php artisan schedule:list`
    - Verify timer updates in database

5. **Role-Based Access Issues**:

    - Ensure roles are seeded: `php artisan db:seed --class=AdminUserSeeder`
    - Check user roles in database: `SELECT * FROM users JOIN roles ON users.role_id = roles.id`
    - Verify middleware is applied correctly
    - Clear cache: `php artisan config:cache`

6. **Asset Issues**:
    - Run `npm run build` for production
    - Use `npm run dev` for development
    - Clear cache: `php artisan optimize:clear`

### Getting Help

-   Check the [Laravel Documentation](https://laravel.com/docs)
-   Review [Laravel Reverb Documentation](https://reverb.laravel.com)
-   Create an issue in the repository for bugs
-   Join the Laravel community for support

## 🎯 Demo Scenarios

### Scenario 1: Admin Match Management

1. Login as admin (`admin@admin.com` / `password`)
2. Create a new match: Manchester United vs Liverpool
3. Set status to "In Progress" - timer starts automatically
4. Open control panel in one browser tab
5. Open live view in another tab
6. Update scores and watch real-time synchronization
7. Observe automatic timer updates every minute

### Scenario 2: Role-Based Access Testing

1. Register a new user account (gets Client role)
2. Login and notice limited interface (no admin buttons)
3. Try accessing control panel - see "View Only Mode"
4. Open live scores page - works normally
5. Login as admin in different browser - see full controls
6. Compare the different user experiences

### Scenario 3: Multi-Match Dashboard with Timers

1. Create multiple matches with different statuses
2. Set some matches to "In Progress" to start timers
3. Open the main matches dashboard
4. Watch multiple timers update simultaneously
5. Use control panels to update different matches
6. Observe live updates on main dashboard

### Scenario 4: Public Viewing Experience

1. Share the live scores URL: `/football/live-scores`
2. Viewers can watch without authentication
3. Admin updates scores in real-time
4. All viewers see updates instantly
5. Automatic timer updates visible to all users
6. No page refresh required

---

**Built with ❤️ using Laravel, Reverb, and modern web technologies**

## 🆕 Recent Updates

### Version 2.0 Features

-   ✅ **Role-Based Access Control**: Admin vs Client user permissions
-   ✅ **Automatic Timer System**: Match timers run automatically with scheduled commands
-   ✅ **Enhanced WebSocket Events**: Timer updates broadcast in real-time
-   ✅ **Login Redirect Optimization**: Users land on football matches after authentication
-   ✅ **Improved User Registration**: Automatic Client role assignment
-   ✅ **Enhanced Security**: Controller middleware protection for admin functions
-   ✅ **UI/UX Improvements**: Role-based interface elements and navigation

### Key Security Features

-   Middleware-level protection for admin routes
-   Role-based UI rendering
-   Automatic 403 responses for unauthorized access
-   Client-side validation with role checks

### Performance Enhancements

-   Efficient timer update system (runs every minute)
-   Optimized WebSocket broadcasting
-   Reduced database queries with role relationships
-   Improved frontend JavaScript with role-based logic

For questions or support, please create an issue in the repository.
