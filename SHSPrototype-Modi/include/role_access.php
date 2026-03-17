<?php
/**
 * Role-Based Access Control (RBAC) System
 * Determines user role and validates access to pages
 */

// Prevent redeclaration of functions if this file is included multiple times
if (!function_exists('getCurrentUserRole')) {

// Get current user's role
function getCurrentUserRole() {
    if (isset($_SESSION['lrn']) && !empty($_SESSION['lrn'])) {
        return 'STUDENT';
    }
    
    // Check if user is an employee (need to get employee_type from DB)
    if (isset($_SESSION['employee_id']) && !empty($_SESSION['employee_id'])) {
        global $conn;
        if (!$conn) {
            return null;
        }
        
        $emp_id = $_SESSION['employee_id'];
        $stmt = $conn->prepare("SELECT employee_type FROM employee_info WHERE employee_id = ?");
        $stmt->bind_param("s", $emp_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $employee = $result->fetch_assoc();
        $stmt->close();
        
        if ($employee && !empty($employee['employee_type'])) {
            // Map employee_type to role
            $employee_type = strtoupper($employee['employee_type']);
            
            if (strpos($employee_type, 'TEACHER') !== false || strpos($employee_type, 'INSTRUCTOR') !== false) {
                return 'TEACHER';
            } elseif (strpos($employee_type, 'SECURITY') !== false) {
                return 'SECURITY';
            } else {
                // Administrative, Maintenance, Cafeteria, Librarian, Nurse, IT Support, Other
                return 'OTHER_PERSONNEL';
            }
        }
    }
    
    return null;
}

// Define access control matrix
$access_matrix = [
    'STUDENT' => [
        'about-us.php',
        'student_form.php',
        'profile.php',
        'saved-qr-codes.php'
    ],
    'TEACHER' => [
        'about-us.php',
        'personnel_form.php',
        'profile.php',
        'saved-qr-codes.php',
        'attendance-log.php'
    ],
    'SECURITY' => [
        'about-us.php',
        'attendance-log.php',
        'scanner.php'
    ],
    'OTHER_PERSONNEL' => [
        'about-us.php',
        'personnel_form.php',
        'profile.php',
        'saved-qr-codes.php'
    ]
];

// Check if user has access to a specific page
function hasAccessToPage($page_name) {
    global $access_matrix;
    
    $user_role = getCurrentUserRole();
    
    if (!$user_role) {
        return false;
    }
    
    if (!isset($access_matrix[$user_role])) {
        return false;
    }
    
    return in_array($page_name, $access_matrix[$user_role]);
}

// Verify access and redirect if unauthorized
function verifyPageAccess($page_name) {
    if (!hasAccessToPage($page_name)) {
        // Log unauthorized access attempt for security
        error_log("[v0] Unauthorized access attempt to $page_name by user");
        
        // Redirect to about-us.php (accessible to all authenticated users)
        header("Location: about-us.php");
        exit();
    }
}

// Get all pages accessible to current user
function getAccessiblePages() {
    global $access_matrix;
    
    $user_role = getCurrentUserRole();
    
    if (!$user_role || !isset($access_matrix[$user_role])) {
        return [];
    }
    
    return $access_matrix[$user_role];
}

// Get current user's role (for display purposes)
function getUserRoleLabel() {
    $role = getCurrentUserRole();
    
    if (!$role) {
        return 'Guest';
    }
    
    $labels = [
        'STUDENT' => 'Student',
        'TEACHER' => 'Teacher',
        'SECURITY' => 'Security Personnel',
        'OTHER_PERSONNEL' => 'Staff'
    ];
    
    return isset($labels[$role]) ? $labels[$role] : 'User';
}

} // End of function_exists guard
?>
