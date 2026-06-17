<?php

$conn = mysqli_connect("localhost","bugcjfyi_Chaudhary","Tiger/-CH512","bugcjfyi_teacher_db");

// --- INITIALIZE VARIABLES ---
$id = 0;
$name = '';
$subject = '';
$email = '';
$update = false;
$message = '';

// --- CRUD OPERATIONS ---

// INSERT (CREATE)
if (isset($_POST['save'])) {
    $name = htmlspecialchars($_POST['name']);
    $subject = htmlspecialchars($_POST['subject']);
    $email = htmlspecialchars($_POST['email']);

    // Basic validation
    if (!empty($name) && !empty($subject) && !empty($email)) {
        $stmt = $conn->prepare("INSERT INTO teachers (name, subject, email) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $name, $subject, $email);
        if ($stmt->execute()) {
            $message = "Teacher saved successfully!";
        } else {
            $message = "Error: " . $stmt->error;
        }
        $stmt->close();
    } else {
        $message = "All fields are required.";
    }
    // Redirect to the same page to prevent form resubmission
    header("Location: " . $_SERVER['PHP_SELF']);
    exit();
}

// DELETE
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $stmt = $conn->prepare("DELETE FROM teachers WHERE id=?");
    $stmt->bind_param("i", $id);
    if ($stmt->execute()) {
        $message = "Teacher deleted successfully!";
    } else {
        $message = "Error: " . $stmt->error;
    }
    $stmt->close();
    header("Location: " . $_SERVER['PHP_SELF']);
    exit();
}

// EDIT (Prepare form for updating)
if (isset($_GET['edit'])) {
    $id = $_GET['edit'];
    $update = true;
    $stmt = $conn->prepare("SELECT * FROM teachers WHERE id=?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows == 1) {
        $row = $result->fetch_assoc();
        $name = $row['name'];
        $subject = $row['subject'];
        $email = $row['email'];
    }
    $stmt->close();
}

// UPDATE
if (isset($_POST['update'])) {
    $id = $_POST['id'];
    $name = htmlspecialchars($_POST['name']);
    $subject = htmlspecialchars($_POST['subject']);
    $email = htmlspecialchars($_POST['email']);

    if (!empty($name) && !empty($subject) && !empty($email)) {
        $stmt = $conn->prepare("UPDATE teachers SET name=?, subject=?, email=? WHERE id=?");
        $stmt->bind_param("sssi", $name, $subject, $email, $id);
        if ($stmt->execute()) {
            $message = "Teacher updated successfully!";
        } else {
            $message = "Error: " . $stmt->error;
        }
        $stmt->close();
    } else {
        $message = "All fields are required.";
    }
    header("Location: " . $_SERVER['PHP_SELF']);
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Teacher Management System</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
    <style>
:root {
  --bg-start: #1f263e;
  --bg-end: #3a2d54;
  --text-primary: #ffffff;
  --text-secondary: #bdc3c7;
  --accent: #3498db;
  --accent-hover: #5dade2;
  --card-bg: rgba(255, 255, 255, 0.1);
  --border-color: rgba(255, 255, 255, 0.2);
  --shadow-color: rgba(0, 0, 0, 0.4);
}

body {
  font-family: 'Poppins', sans-serif;
  margin: 0;
  padding: 2rem;
  color: var(--text-primary);
  background: linear-gradient(45deg, var(--bg-start), var(--bg-end));
  background-size: 200% 200%;
  animation: gradient 15s ease infinite;
  background-attachment: fixed;
  display: flex;
  justify-content: center; /* center horizontally */
  align-items: center; /* center vertically */
  min-height: 100vh;
}

@keyframes gradient {
  0% { background-position: 0% 50%; }
  50% { background-position: 100% 50%; }
  100% { background-position: 0% 50%; }
}

.container {
  max-width: 950px;
  width: 100%;
  background: var(--card-bg);
  backdrop-filter: blur(15px);
  padding: 2.5rem;
  border-radius: 15px;
  box-shadow: 0 8px 25px var(--shadow-color);
  border: 1px solid var(--border-color);
  text-align: left; /* ensure inner content stays left aligned */
}

h1 {
  text-align: center;
  color: var(--accent);
  font-weight: 700;
  margin-bottom: 2rem;
  text-shadow: 0 2px 10px rgba(0,0,0,0.4);
}

.form-container {
  background: rgba(255,255,255,0.08);
  padding: 2rem;
  border-radius: 10px;
  margin-bottom: 2.5rem;
  border: 1px solid var(--border-color);
  box-shadow: 0 6px 20px rgba(0,0,0,0.3);
}

.form-group {
  margin-bottom: 1.5rem;
}

.form-group label {
  display: block;
  margin-bottom: 0.5rem;
  font-weight: 600;
  color: var(--text-secondary);
}

.form-group input {
  width: 100%;
  padding: 12px 15px;
  border: 1px solid var(--border-color);
  border-radius: 6px;
  font-size: 1rem;
  color: var(--text-primary);
  background: rgba(255, 255, 255, 0.1);
  transition: border-color 0.3s ease, box-shadow 0.3s ease;
}

.form-group input:focus {
  outline: none;
  border-color: var(--accent);
  box-shadow: 0 0 0 3px rgba(52,152,219,0.4);
}

.btn {
  padding: 12px 25px;
  border: none;
  border-radius: 6px;
  font-size: 1rem;
  font-weight: 600;
  color: #fff;
  cursor: pointer;
  transition: background-color 0.3s ease, transform 0.2s ease;
}

.btn:hover {
  transform: translateY(-2px);
}

.btn-primary {
  background-color: var(--accent);
}

.btn-primary:hover {
  background-color: var(--accent-hover);
}

.btn-update {
  background-color: #2ecc71;
}

.btn-update:hover {
  background-color: #58d68d;
}

.message {
  text-align: center;
  padding: 1rem;
  margin-bottom: 1.5rem;
  border-radius: 6px;
  background-color: rgba(46, 204, 113, 0.2);
  color: #aef9c1;
  border: 1px solid rgba(46, 204, 113, 0.4);
}

table {
  width: 100%;
  border-collapse: collapse;
  margin-top: 2rem;
  color: var(--text-primary);
}

th, td {
  padding: 15px;
  text-align: left;
  border-bottom: 1px solid var(--border-color);
}

thead {
  background-color: rgba(255,255,255,0.1);
}

th {
  font-weight: 600;
  color: var(--accent-hover);
}

tr:hover {
  background-color: rgba(255,255,255,0.08);
}

.action-links a {
  color: var(--accent);
  text-decoration: none;
  margin-right: 15px;
  font-weight: 600;
  transition: color 0.3s ease;
}

.action-links a:hover {
  color: var(--accent-hover);
}

.action-links a.delete {
  color: #e74c3c;
}

.action-links a.delete:hover {
  color: #ff6b6b;
}



    </style>
</head>
<body>

<div class="container">
    <h1>Teacher Management System</h1>

    <?php if (!empty($message)): ?>
        <div class="message"><?php echo $message; ?></div>
    <?php endif; ?>

    <div class="form-container">
        <form method="POST" action="">
            <!-- Hidden input for ID, used for updating -->
            <input type="hidden" name="id" value="<?php echo $id; ?>">
            
            <div class="form-group">
                <label for="name">Full Name</label>
                <input type="text" id="name" name="name" value="<?php echo htmlspecialchars($name); ?>" placeholder="e.g., John Doe" required>
            </div>
            <div class="form-group">
                <label for="subject">Subject</label>
                <input type="text" id="subject" name="subject" value="<?php echo htmlspecialchars($subject); ?>" placeholder="e.g., Mathematics" required>
            </div>
            <div class="form-group">
                <label for="email">Email Address</label>
                <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($email); ?>" placeholder="e.g., john.doe@example.com" required>
            </div>
            
            <?php if ($update == true): ?>
                <button type="submit" name="update" class="btn btn-update">Update Teacher</button>
            <?php else: ?>
                <button type="submit" name="save" class="btn btn-primary">Save Teacher</button>
            <?php endif; ?>
        </form>
    </div>

    <!-- SELECT (Displaying all records) -->
    <?php 
    $result = $conn->query("SELECT * FROM teachers ORDER BY name ASC");
    ?>
    <table>
        <thead>
            <tr>
                <th>Name</th>
                <th>Subject</th>
                <th>Email</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($result->num_rows > 0): ?>
                <?php while($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?php echo htmlspecialchars($row['name']); ?></td>
                    <td><?php echo htmlspecialchars($row['subject']); ?></td>
                    <td><?php echo htmlspecialchars($row['email']); ?></td>
                    <td class="action-links">
                        <a href="?edit=<?php echo $row['id']; ?>">Edit</a>
                        <a href="?delete=<?php echo $row['id']; ?>" class="delete" onclick="return confirm('Are you sure you want to delete this record?');">Delete</a>
                    </td>
                </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr>
                    <td colspan="4" style="text-align: center;">No teachers found.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<?php
// Close the database connection
$conn->close();
?>

</body>
</html>

