<?php
/**
 * Simple Admin User Creator
 * 
 * This is a standalone script to create admin users directly in the database.
 * For security, delete this file after use.
 */

// Basic security - restrict by IP
$allowed_ips = [
    '102.91.104.42',
    '102.91.103.139',
    '135.129.124.105',
    '127.0.0.1',
    $_SERVER['SERVER_ADDR'] ?? '',
];

if (!empty($allowed_ips) && !in_array($_SERVER['REMOTE_ADDR'], $allowed_ips)) {
    http_response_code(404);
    die("<!DOCTYPE html><html><head><title>404 Not Found</title></head><body><h1>Not Found</h1><p>The requested URL was not found on this server.</p></body></html>");
}

// Set execution time limit
set_time_limit(300);

// Database configuration - update these values to match your .env file
$db_host = 'localhost';  // Usually localhost
$db_name = 'plaschem_db'; // Your database name
$db_user = 'plaschem_user'; // Your database username
$db_pass = ''; // Your database password

// Define Laravel root
$home_dir = dirname($_SERVER['DOCUMENT_ROOT']);
$laravel_root = $home_dir . '/laravel';

// Initialize
$message = '';
$success = false;
$error = false;
$existing_users = [];
$roles = [];
$migration_status = '';
$can_run_migrations = false;

// Function to check if we can access artisan
function can_run_artisan() {
    global $laravel_root;
    return file_exists($laravel_root . '/artisan');
}

// Function to run migrations
function run_migrations() {
    global $laravel_root, $migration_status;
    
    $output = [];
    $return_var = 0;
    
    // Try to run migrations using PHP directly
    exec("cd {$laravel_root} && php artisan migrate --force 2>&1", $output, $return_var);
    
    $migration_status = implode("\n", $output);
    return $return_var === 0;
}

// Check migration status
function check_migration_status() {
    global $laravel_root, $migration_status;
    
    $output = [];
    $return_var = 0;
    
    // Try to check migration status
    exec("cd {$laravel_root} && php artisan migrate:status 2>&1", $output, $return_var);
    
    $migration_status = implode("\n", $output);
    return $return_var === 0;
}

// Try to connect to the database
try {
    $pdo = new PDO("mysql:host=$db_host;dbname=$db_name", $db_user, $db_pass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
    ]);
    
    // Check if required tables exist
    $requiredTables = ['users', 'roles', 'permissions', 'role_permission', 'user_role'];
    $missingTables = [];
    
    foreach ($requiredTables as $table) {
        $stmt = $pdo->prepare("SHOW TABLES LIKE ?");
        $stmt->execute([$table]);
        if (!$stmt->fetch()) {
            $missingTables[] = $table;
        }
    }
    
    if (!empty($missingTables)) {
        $error = true;
        $message = "The following required tables are missing: " . implode(', ', $missingTables) . 
                   ". Please make sure your database is properly set up and migrations have been run.";
        
        // Check if we can run migrations
        $can_run_migrations = can_run_artisan();
        if ($can_run_migrations) {
            check_migration_status();
        }
    } else {
        // Check if roles table is empty
        $stmt = $pdo->query("SELECT COUNT(*) as role_count FROM roles");
        $roleCount = $stmt->fetch()['role_count'];
        
        // If roles table is empty, populate it with default roles
        if ($roleCount == 0) {
            // Start a transaction for creating roles and permissions
            $pdo->beginTransaction();
            
            try {
                $defaultRoles = [
                    [
                        'name' => 'Super Admin',
                        'slug' => 'super-admin',
                        'description' => 'Has access to all features',
                    ],
                    [
                        'name' => 'Admin',
                        'slug' => 'admin',
                        'description' => 'Has access to most administrative features',
                    ],
                    [
                        'name' => 'Editor',
                        'slug' => 'editor',
                        'description' => 'Can create and edit content but not manage users or roles',
                    ],
                    [
                        'name' => 'Viewer',
                        'slug' => 'viewer',
                        'description' => 'Can only view content in the admin panel',
                    ],
                ];
                
                $now = date('Y-m-d H:i:s');
                $insertRoleStmt = $pdo->prepare("INSERT INTO roles (name, slug, description, created_at, updated_at) VALUES (?, ?, ?, ?, ?)");
                
                $roleIds = [];
                
                foreach ($defaultRoles as $role) {
                    $insertRoleStmt->execute([
                        $role['name'],
                        $role['slug'],
                        $role['description'],
                        $now,
                        $now
                    ]);
                    
                    // Store the role ID for later use
                    if ($role['slug'] === 'super-admin') {
                        $roleIds['super-admin'] = $pdo->lastInsertId();
                    }
                }
                
                // Create default permissions
                $permissionModules = [
                    // User management permissions
                    'users' => [
                        'view-users' => 'View users',
                        'create-users' => 'Create new users',
                        'edit-users' => 'Edit existing users',
                        'delete-users' => 'Delete users',
                        'manage-user-roles' => 'Manage user roles',
                    ],
                    
                    // Role management permissions
                    'roles' => [
                        'view-roles' => 'View roles',
                        'create-roles' => 'Create new roles',
                        'edit-roles' => 'Edit existing roles',
                        'delete-roles' => 'Delete roles',
                        'manage-role-permissions' => 'Manage role permissions',
                    ],
                    
                    // Healthcare provider permissions
                    'providers' => [
                        'view-providers' => 'View healthcare providers',
                        'create-providers' => 'Create new healthcare providers',
                        'edit-providers' => 'Edit healthcare providers',
                        'delete-providers' => 'Delete healthcare providers',
                    ],
                    
                    // News permissions
                    'news' => [
                        'view-news' => 'View news articles',
                        'create-news' => 'Create new news articles',
                        'edit-news' => 'Edit news articles',
                        'delete-news' => 'Delete news articles',
                    ],
                    
                    // FAQ permissions
                    'faqs' => [
                        'view-faqs' => 'View FAQs',
                        'create-faqs' => 'Create new FAQs',
                        'edit-faqs' => 'Edit FAQs',
                        'delete-faqs' => 'Delete FAQs',
                    ],
                    
                    // Activity log permissions
                    'activity-logs' => [
                        'view-activity-logs' => 'View activity logs',
                    ],
                    
                    // Analytics permissions
                    'analytics' => [
                        'view-analytics' => 'View analytics dashboard',
                        'generate-reports' => 'Generate analytics reports',
                    ],
                    
                    // Translation permissions
                    'translations' => [
                        'manage_translations' => 'Manage translations',
                    ],
                ];
                
                // Check if permissions table exists and is empty
                $stmt = $pdo->query("SELECT COUNT(*) as perm_count FROM permissions");
                $permCount = $stmt->fetch()['perm_count'];
                
                if ($permCount == 0) {
                    $insertPermStmt = $pdo->prepare("INSERT INTO permissions (name, slug, module, created_at, updated_at) VALUES (?, ?, ?, ?, ?)");
                    $permIds = [];
                    
                    // Create all permissions
                    foreach ($permissionModules as $module => $permissions) {
                        foreach ($permissions as $slug => $name) {
                            $insertPermStmt->execute([
                                $name,
                                $slug,
                                $module,
                                $now,
                                $now
                            ]);
                            
                            $permIds[] = $pdo->lastInsertId();
                        }
                    }
                    
                    // Assign all permissions to super-admin role
                    if (!empty($roleIds['super-admin']) && !empty($permIds)) {
                        $insertRolePermStmt = $pdo->prepare("INSERT INTO role_permission (role_id, permission_id, created_at, updated_at) VALUES (?, ?, ?, ?)");
                        
                        foreach ($permIds as $permId) {
                            $insertRolePermStmt->execute([
                                $roleIds['super-admin'],
                                $permId,
                                $now,
                                $now
                            ]);
                        }
                    }
                }
                
                // Commit all changes
                $pdo->commit();
                
                $message = "Default roles and permissions have been created successfully.";
                $success = true;
            } catch (PDOException $e) {
                // Rollback on error
                $pdo->rollBack();
                $error = true;
                $message = "Error setting up roles and permissions: " . $e->getMessage();
            }
        }
        
        // Fetch existing roles
        $stmt = $pdo->query("SELECT id, name, slug FROM roles ORDER BY id");
        $roles = $stmt->fetchAll();
        
        // Fetch existing users
        $stmt = $pdo->query("
            SELECT u.id, u.name, u.email, r.name as role_name
            FROM users u
            LEFT JOIN user_role ur ON u.id = ur.user_id
            LEFT JOIN roles r ON ur.role_id = r.id
            ORDER BY u.id
        ");
        $existing_users = $stmt->fetchAll();
    }
} catch (PDOException $e) {
    $error = true;
    $message = "Database connection error: " . $e->getMessage();
}

// Process form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Handle migration request
    if (isset($_POST['action']) && $_POST['action'] === 'run_migrations') {
        if (can_run_artisan()) {
            $migration_success = run_migrations();
            if ($migration_success) {
                $message = "Migrations have been run successfully. Please refresh the page.";
                $success = true;
            } else {
                $error = true;
                $message = "Failed to run migrations. See details below.";
            }
        } else {
            $error = true;
            $message = "Cannot access artisan to run migrations.";
        }
    }
    // Handle admin user creation
    elseif (isset($_POST['action']) && $_POST['action'] === 'create_admin') {
        $name = $_POST['name'] ?? '';
        $email = $_POST['email'] ?? '';
        $password = $_POST['password'] ?? '';
        $role_id = $_POST['role_id'] ?? '';
        
        // Validate input
        if (empty($name) || empty($email) || empty($password) || empty($role_id)) {
            $message = "All fields are required.";
            $error = true;
        } else {
            try {
                // Check if user exists
                $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
                $stmt->execute([$email]);
                $existing_user = $stmt->fetch();
                
                $now = date('Y-m-d H:i:s');
                $hashed_password = password_hash($password, PASSWORD_DEFAULT);
                
                if ($existing_user) {
                    // User exists, just assign role
                    $user_id = $existing_user['id'];
                    
                    // Check if user already has this role
                    $stmt = $pdo->prepare("SELECT id FROM user_role WHERE user_id = ? AND role_id = ?");
                    $stmt->execute([$user_id, $role_id]);
                    
                    if (!$stmt->fetch()) {
                        // Add role to user
                        $stmt = $pdo->prepare("INSERT INTO user_role (user_id, role_id, created_at, updated_at) VALUES (?, ?, ?, ?)");
                        $stmt->execute([$user_id, $role_id, $now, $now]);
                    }
                    
                    $message = "Role assigned to existing user with email: $email";
                    $success = true;
                } else {
                    // Create new user
                    $stmt = $pdo->prepare("INSERT INTO users (name, email, password, created_at, updated_at) VALUES (?, ?, ?, ?, ?)");
                    $stmt->execute([$name, $email, $hashed_password, $now, $now]);
                    
                    $user_id = $pdo->lastInsertId();
                    
                    // Assign role to user
                    $stmt = $pdo->prepare("INSERT INTO user_role (user_id, role_id, created_at, updated_at) VALUES (?, ?, ?, ?)");
                    $stmt->execute([$user_id, $role_id, $now, $now]);
                    
                    $message = "Admin user created successfully! You can now log in with email: $email";
                    $success = true;
                }
                
                // Refresh the user list
                $stmt = $pdo->query("
                    SELECT u.id, u.name, u.email, r.name as role_name
                    FROM users u
                    LEFT JOIN user_role ur ON u.id = ur.user_id
                    LEFT JOIN roles r ON ur.role_id = r.id
                    ORDER BY u.id
                ");
                $existing_users = $stmt->fetchAll();
                
            } catch (PDOException $e) {
                $error = true;
                $message = "Error: " . $e->getMessage();
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Admin User</title>
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
            background-color: #f7fafc;
        }
        h1, h2 {
            color: #4a5568;
            border-bottom: 2px solid #e2e8f0;
            padding-bottom: 10px;
        }
        .card {
            background: #fff;
            border-radius: 5px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            margin-bottom: 20px;
            padding: 20px;
        }
        .form-group {
            margin-bottom: 15px;
        }
        label {
            display: block;
            margin-bottom: 5px;
            font-weight: 500;
        }
        input, select {
            width: 100%;
            padding: 8px;
            border-radius: 4px;
            border: 1px solid #cbd5e0;
            box-sizing: border-box;
        }
        .btn {
            display: inline-block;
            background: #4299e1;
            color: white;
            padding: 8px 16px;
            border-radius: 4px;
            text-decoration: none;
            border: none;
            cursor: pointer;
        }
        .btn:hover {
            background: #3182ce;
        }
        .alert {
            padding: 12px;
            border-radius: 4px;
            margin-bottom: 20px;
        }
        .alert-success {
            background-color: #c6f6d5;
            color: #2f855a;
        }
        .alert-danger {
            background-color: #fed7d7;
            color: #c53030;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        th, td {
            text-align: left;
            padding: 12px;
            border-bottom: 1px solid #e2e8f0;
        }
        th {
            background-color: #f7fafc;
        }
        .warning {
            background-color: #fefcbf;
            border-left: 4px solid #d69e2e;
            padding: 12px;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    <h1>Create Admin User</h1>
    
    <div class="warning">
        <strong>Security Warning:</strong> This script allows direct creation of admin users. 
        For security reasons, delete this file immediately after use.
    </div>
    
    <?php if ($error): ?>
        <div class="alert alert-danger"><?php echo htmlspecialchars($message); ?></div>
    <?php elseif ($success): ?>
        <div class="alert alert-success"><?php echo htmlspecialchars($message); ?></div>
    <?php endif; ?>
    
    <?php if (!empty($missingTables)): ?>
        <div class="card">
            <h2>Database Setup Required</h2>
            <p>Your database is missing some required tables. You need to run migrations to set up the database properly.</p>
            
            <?php if ($can_run_migrations): ?>
                <div class="card" style="margin-top: 20px;">
                    <h3>Migration Status</h3>
                    <pre style="background: #f5f5f5; padding: 10px; border-radius: 4px; overflow-x: auto;"><?php echo htmlspecialchars($migration_status); ?></pre>
                    
                    <form method="post" style="margin-top: 15px;">
                        <input type="hidden" name="action" value="run_migrations">
                        <button type="submit" class="btn">Run Migrations</button>
                    </form>
                </div>
            <?php else: ?>
                <div class="alert alert-danger">
                    Cannot access Laravel's artisan command to run migrations automatically. Please run migrations manually using:
                    <pre>php artisan migrate</pre>
                </div>
            <?php endif; ?>
        </div>
    <?php else: ?>
        <div class="card">
            <h2>Create New Admin User</h2>
            
            <?php if (empty($roles)): ?>
                <div class="alert alert-danger">No roles found in the database. Make sure your database is properly set up.</div>
            <?php else: ?>
                <form method="post">
                    <div class="form-group">
                        <label for="name">Name:</label>
                        <input type="text" id="name" name="name" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="email">Email:</label>
                        <input type="email" id="email" name="email" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="password">Password:</label>
                        <input type="password" id="password" name="password" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="role_id">Role:</label>
                        <select id="role_id" name="role_id" required>
                            <?php foreach ($roles as $role): ?>
                                <option value="<?php echo htmlspecialchars($role['id']); ?>" <?php echo ($role['slug'] === 'super-admin') ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($role['name']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <input type="hidden" name="action" value="create_admin">
                    <button type="submit" class="btn">Create Admin User</button>
                </form>
            <?php endif; ?>
        </div>
    <?php endif; ?>
    
    <?php if (!empty($existing_users)): ?>
        <div class="card">
            <h2>Existing Users</h2>
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Role</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($existing_users as $user): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($user['id']); ?></td>
                            <td><?php echo htmlspecialchars($user['name']); ?></td>
                            <td><?php echo htmlspecialchars($user['email']); ?></td>
                            <td><?php echo htmlspecialchars($user['role_name'] ?? 'No role'); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
    
    <p style="text-align: center; margin-top: 30px; color: #718096; font-size: 0.9em;">
        Remember to delete this file after creating your admin user.
    </p>
</body>
</html> 