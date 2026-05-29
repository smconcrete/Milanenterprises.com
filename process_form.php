<?php
// Check if the form was submitted via POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    // 1. Set your destination email address
    $to_email = "milanenterprises.blr@gmail.com";
    
    // 2. Sanitize and gather form data
    $name = filter_var(trim($_POST["name"]), FILTER_SANITIZE_STRING);
    $phone = filter_var(trim($_POST["phone"]), FILTER_SANITIZE_STRING);
    $email = filter_var(trim($_POST["email"]), FILTER_SANITIZE_EMAIL);
    $position = filter_var(trim($_POST["position"]), FILTER_SANITIZE_STRING);
    $message = filter_var(trim($_POST["message"]), FILTER_SANITIZE_STRING);
    
    // Validate required fields
    if (empty($name) || empty($phone)) {
        die("Error: Name and Phone Number are required.");
    }
    
    $subject = "New Application/Inquiry: " . $name;
    
    // 3. Prepare the email body text
    $email_body = "You have received a new submission from the Milan Enterprises website.\n\n";
    $email_body .= "Name: " . $name . "\n";
    $email_body .= "Phone: " . $phone . "\n";
    $email_body .= "Email: " . (!empty($email) ? $email : "Not provided") . "\n";
    $email_body .= "Position: " . (!empty($position) ? $position : "Not specified") . "\n\n";
    $email_body .= "Message/Cover Letter:\n" . (!empty($message) ? $message : "No message provided") . "\n";

    // 4. Handle File Attachment and Headers
    $boundary = md5(time()); // Create a unique boundary for the multipart email
    
    // Basic headers
    $headers = "From: noreply@yourwebsite.com\r\n"; // CHANGE THIS to an email on your domain
    if (!empty($email)) {
        $headers .= "Reply-To: " . $email . "\r\n";
    }
    $headers .= "MIME-Version: 1.0\r\n";
    $headers .= "Content-Type: multipart/mixed; boundary=\"" . $boundary . "\"\r\n";
    
    // Message Body Part
    $multipart_body = "--" . $boundary . "\r\n";
    $multipart_body .= "Content-Type: text/plain; charset=UTF-8\r\n";
    $multipart_body .= "Content-Transfer-Encoding: 7bit\r\n\r\n";
    $multipart_body .= $email_body . "\r\n\r\n";
    
    // File Attachment Part (if a file was uploaded without errors)
    if (isset($_FILES['resume']) && $_FILES['resume']['error'] == UPLOAD_ERR_OK) {
        
        $file_tmp_name = $_FILES['resume']['tmp_name'];
        $file_name = $_FILES['resume']['name'];
        $file_type = $_FILES['resume']['type'];
        
        // Read the file and encode it for email transmission
        $file_content = file_get_contents($file_tmp_name);
        $encoded_content = chunk_split(base64_encode($file_content));
        
        $multipart_body .= "--" . $boundary . "\r\n";
        $multipart_body .= "Content-Type: " . $file_type . "; name=\"" . $file_name . "\"\r\n";
        $multipart_body .= "Content-Disposition: attachment; filename=\"" . $file_name . "\"\r\n";
        $multipart_body .= "Content-Transfer-Encoding: base64\r\n\r\n";
        $multipart_body .= $encoded_content . "\r\n\r\n";
    }
    
    $multipart_body .= "--" . $boundary . "--"; // End of multipart message
    
    // 5. Send the Email
    if (mail($to_email, $subject, $multipart_body, $headers)) {
        // Success: Redirect back to the website or show a success message
        echo "<h2>Thank You!</h2><p>Your application has been successfully submitted. We will be in touch shortly.</p>";
        echo "<a href='index.html'>Return to Homepage</a>"; // Update 'index.html' to your actual HTML file name
    } else {
        // Failure
        echo "<h2>Oops!</h2><p>Something went wrong, and we couldn't send your application. Please try calling us directly.</p>";
    }
    
} else {
    // If someone tries to access this file directly without submitting the form
    echo "Access Denied.";
}
?>