<?php
include ('connection.php');
include "log_function.php";

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
//PHPMailer
require "PHPMailer/src/Exception.php";
require "PHPMailer/src/PHPMailer.php";
require "PHPMailer/src/SMTP.php";

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DAMS</title>
    <!-- font awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta2/css/all.min.css" integrity="sha512-YWzhKL2whUzgiheMoBFwW8CKV4qpHQAEuvilg9FAn5VJUDwKZZxkJNuGM4XkWuk94WCrrwslk8yWNGmY1EduTA==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <!-- normalize css -->
    <link rel = "stylesheet" href = "css/normalize.css">
    <!-- custom css -->
    <link rel = "stylesheet" href = "css/main.css">
</head>
<body>
    

    <!-- header -->
    <header class = "header bg-blue">
        <nav class = "navbar bg-white">
            <div class = "container flex">
                <a href = "index.php" class = "navbar-brand text-lg flex">
                     <img src = "images/logo1.png" alt = "site logo">
                    DAMS
                </a>
                <button type = "button" class = "navbar-show-btn btn-menu">
                    <i class="fa fa-bars"></i>
                </button>

                <div class = "navbar-collapse bg-white">
                    <button type = "button" class = "navbar-hide-btn">
                        <img src = "images/close-icon.png">
                    </button>
                    <ul class = "navbar-nav">
                        <li class = "nav-item">
                            <a href = "#" class = "nav-link">Home</a>
                        </li>
                        <li class = "nav-item">
                            <a href = "#about" class = "nav-link">About</a>
                        </li>
                        <li class = "nav-item">
                            <a href = "#services" class = "nav-link">Operation</a>
                        </li>
                        <li class = "nav-item">
                            <a href = "#doc-panel" class = "nav-link">Doctors</a>
                        </li>
                        <li class = "nav-item">
                            <a href = "#contact" class = "nav-link">Contact</a>
                        </li>
                    </ul>

                    <a href="schedules.php" role="button" class="btn-appointment">Book Appointment</a>
                    
                </div> 
            </div>
        </nav>
        
    
        <div class = "header-inner text-white text-center">
            <div class = "container grid">
                <div class = "header-inner-left">
                    <h1><span>Book your Doctor</span></h1>
                    <h1><span>Appointment Online.</span></h1>
                    <p>&nbsp;</p>
                    <span class = "text text-md" style="text-transform: capitalize;">a healthier tomorrow starts today: Schedule your appointment!</span>
                    <span class = "text text-md" style="text-transform: capitalize;">Your wellness, Our expertise: set up your appointment today.</span>
                    <p>&nbsp;</p>
                    <div class = "btn-group">
                        <a href = "schedules.php" class = "btn btn-book">Book An Appointment</a>
                        <?php
                        $admin_id = 100;
                        $stmt = mysqli_prepare($conn, "SELECT admin_email_address,hospital_contact_no FROM admin_table WHERE admin_id = ? LIMIT 1");
                        mysqli_stmt_bind_param($stmt,'i',$admin_id);
                        mysqli_stmt_execute($stmt);
                        $result = mysqli_stmt_get_result($stmt);
                        $row = mysqli_fetch_assoc($result);
                        $contact_no = $row['hospital_contact_no'];
                        $admin_email = $row['admin_email_address'];
                        ?>
                        <a href = "tel:<?php echo $contact_no ?>" class = "btn btn-trans"><i class="fa fa-phone"></i> Call Now</a>
                    </div>
                </div>
                <div class = "header-inner-right">
                    <img src = "images/hdr.png">
                </div>
            </div>
        </div>
    </header>
    <!-- end of header -->

    <main>
         <!-- services section -->
        <section id = "services" class = "services py">
            <div class = "container">
                <div class = "section-head text-center">
                    <h2 class = "lead">How it works!</h2>
                    <p class = "text text-lg">Discover, book and experience personalized healthcare effortlessly with our user-friendly Doctor Appointment Website.</p>
                    <div class = "line-art flex">
                        <div></div>
                        <img src = "images/4-dots.png">
                        <div></div>
                    </div>
                </div>
                <div class = "services-inner text-center grid">
                    <article class = "service-item">
                        <div class = "icon">
                            <i class="fa fa-user"></i>
                        </div>
                        <h3>Create Account and Login</h3>
                        <p class = "text text-sm">Sign up and sign in to the system using registered credentials.</p>
                    </article>

                    <article class = "service-item">
                        <div class = "icon">
                            <i class="fa-solid fa-user-doctor"></i>
                        </div>
                        <h3>Find A Doctor</h3>
                        <p class = "text text-sm">Discover skilled doctors based on specialization.</p>
                    </article>

                    <article class = "service-item">
                        <div class = "icon">
                            <i class="fa fa-calendar-days"></i>
                        </div>
                        <h3>Book Appointment</h3>
                        <p class = "text text-sm">Effortlessly book appointments at your convenience.</p>
                    </article>

                    <article class = "service-item">
                        <div class = "icon">
                            <i class="fa fa-suitcase-medical"></i>
                        </div>
                        <h3>Get Services</h3>
                        <p class = "text text-sm">Receive personalized healthcare services tailored to your needs. </p>
                    </article>
                </div>
            </div>
        </section>
        <!-- end of services section -->

        <!-- banner one -->
        <section id = "banner-one" class = "banner-one text-center">
            <div class = "container text-white">
                <blockquote class = "lead"><i class = "fas fa-quote-left"></i> When you are young and healthy, it never occurs to you that in a single second your whole life could change. <i class = "fas fa-quote-right"></i></blockquote>
                <small class = "text text-sm">- Dr. Luke Mulli</small>
            </div>
        </section>
        <!-- end of banner one -->


        <!-- about section -->
        <section id = "about" class = "about py">
            <div class = "about-inner">
                <div class = "container grid">
                    <div class = "about-left text-center">
                        <div class = "section-head">
                            <h2>About Us</h2>
                            <div class = "border-line"></div>
                        </div>
                        <p>&nbsp;</p>
                        <h1 style="text-align: center;">Healing Starts Here: Your Journey, Our Commitment.</h1>
                        <p class = "text text-lg" style="text-align: justify;">Welcome to a healthcare experience designed around you. Our platform simplifies the path to wellness, connecting you seamlessly with expert care. Your health, our priority.</p>
                        <a href="schedules.php" role="button" class="btn-appointment">Book Appointment</a>
                    </div>
                    
                    <div class = "about-right flex">
                        <div class = "img">
                            <img src = "images/hospital%20bed.jpeg">
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <!-- end of about section -->   

        <!-- banner two section -->
        <section id = "banner-two" class = "banner-two text-center">
            <div class = "container grid">
                <?php
                $stmt = mysqli_prepare($conn, "SELECT COUNT(*) AS total_patients FROM patient_table");
                mysqli_stmt_execute($stmt);
                $total_patients_result = mysqli_stmt_get_result($stmt);
                $total_patients_row = mysqli_fetch_assoc($total_patients_result);
                // Extract the total_patients value from the row
                $total_patients = $total_patients_row['total_patients'];

                $stmt = mysqli_prepare($conn, "SELECT COUNT(*) AS total_doctors FROM doctor_table");
                mysqli_stmt_execute($stmt);
                $total_doctors_result = mysqli_stmt_get_result($stmt);
                $total_doctors_row = mysqli_fetch_assoc($total_doctors_result);
                // Extract the total_patients value from the row
                $total_doctors = $total_doctors_row['total_doctors'];
                ?>
               <div class="stats">
                    <h3 class="lead" style="color: white; font-size: 4rem; font-weight: 500;margin-bottom: 2rem; text-align: center;"><?php echo $total_patients?>+</h3>
                    <p class="text text-md" style="color: white;">Satisfied Patients</p>
                </div>
                <div class="stats">
                    <h3 class="lead" style="color: white; font-size: 4rem; font-weight: 500;margin-bottom: 2rem; text-align: center;"><?php echo $total_doctors?>+</h3>
                    <p class="text text-md" style="color: white;">Specialized Staff</p>
                </div>
                <div class="stats">
                    <h3 class="lead" style="color: white; font-size: 4rem; font-weight: 500;margin-bottom: 2rem; text-align: center;">12+</h3>
                    <p class="text text-md" style="color: white;">Nursing Staff</p>
                </div>
                <div class="stats">
                    <h3 class="lead" style="color: white; font-size: 4rem; font-weight: 500;margin-bottom: 2rem; text-align: center;">24/7</h3>
                    <p class="text text-md" style="color: white;">Emergency Care</p>
                </div>
            </div>
        </section>
        <!-- end of banner two section -->

        <!-- doctors section -->
        <section id = "doc-panel" class = "doc-panel py">
            <div class = "container">
                <div class = "section-head">
                    <h2>Our Doctor Panel</h2>
                </div>

                <?php
                $query = "SELECT * FROM doctor_table";
                $stmt = mysqli_prepare($conn, $query);
                mysqli_stmt_execute($stmt);
                $result = mysqli_stmt_get_result($stmt);
                $rows = mysqli_fetch_all($result, MYSQLI_ASSOC);
                ?>

                <div class="doc-panel-inner grid">
                    <?php foreach ($rows as $row) { ?>
                        <div class="doc-panel-item">
                            <div class="img flex">
                                <!-- Use the actual profile image from the database -->
                                <img src="images/<?php echo basename($row['doctor_profile_image']); ?>" alt="doctor image">
                                <div class="info text-center bg-blue text-white flex">
                                    <!-- Display the doctor's name from the database -->
                                    <p class="lead"><?php echo $row['doctor_name']; ?></p>
                                    <!-- Display the doctor's specialty from the database -->
                                    <p class="text-lg"><?php echo $row['doctor_expert_in']; ?></p>
                                </div>
                            </div>
                        </div>
                    <?php } ?>
                </div>
            </div>
        </section>
        <!-- end of doctors section -->

        <!-- FAQ's section -->
        <section id="FAQs" class="FAQs py">
            <div class="container">
                <div class="section-head text-center">
                    <h2 class="lead">Frequently Asked Questions</h2>
                    <p class="text text-md">Find solutions to common queries about signing up, profile editing, support and data security.</p>
                </div>
                <div class = "about-inner">
                    <div class = "container grid">
                        <div class = "about-left text-center">
                            <div class="faq">
                                <div class="question">
                                    <h3>How do I book an appointment ?</h3>
                                    
                                    <!-- <svg width="15" height="10" viewbox="0 0 42 25">
                                        <path d="M3 3L21 21L39 3" stroke="white" stroke-width="7" stroke-linecap="round"/>
                                    </svg> -->
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"><path d="M12 17.414 3.293 8.707l1.414-1.414L12 14.586l7.293-7.293 1.414 1.414L12 17.414z"/></svg>
                                </div>
                                <div class="answer">
                                    <p>
                                        Log in to your account on the system. Select the desired doctor or specialist. Choose a convenient date and time from the available slots. Confirm your appointment details. Receive confirmation via email.
                                    </p>
                                </div>
                            </div>

                            <div class="faq">
                                <div class="question">
                                    <h3>Can I book for someone else ?</h3>
                                    
                                    <!-- <svg width="15" height="10" viewbox="0 0 42 25">
                                        <path d="M3 3L21 21L39 3" stroke="white" stroke-width="7" stroke-linecap="round"/>
                                    </svg> -->
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"><path d="M12 17.414 3.293 8.707l1.414-1.414L12 14.586l7.293-7.293 1.414 1.414L12 17.414z"/></svg>
                                </div>
                                <div class="answer">
                                    <p>
                                        Yes you can book for someone provided that you are allowed to have the credentials of a registered patient in the system, and you know how to interact with the system.
                                    </p>
                                </div>
                            </div>

                            <div class="faq">
                                <div class="question">
                                    <h3>What information is needed for booking ?</h3>
                                    
                                    <!-- <svg width="15" height="10" viewbox="0 0 42 25">
                                        <path d="M3 3L21 21L39 3" stroke="white" stroke-width="7" stroke-linecap="round"/>
                                    </svg> -->
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"><path d="M12 17.414 3.293 8.707l1.414-1.414L12 14.586l7.293-7.293 1.414 1.414L12 17.414z"/></svg>
                                </div>
                                <div class="answer">
                                    <p>
                                        You will require the following details: Patient's full name, Patient's date of birth, Contact information (email address, phone number), Preferred doctor or specialist, Desired appointment date and time, and the reason for the appointment.
                                    </p>
                                </div>
                            </div>

                            
                        </div>
                        
                        <div class = "FAQ-right text-center">
                            <div class="faq">
                                <div class="question">
                                    <h3>What payment methods are accepted ?</h3>
                                
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"><path d="M12 17.414 3.293 8.707l1.414-1.414L12 14.586l7.293-7.293 1.414 1.414L12 17.414z"/></svg>
                                </div>
                                <div class="answer">
                                    <p>
                                        Payments are either cash or cashless via M-PESA non-refundable with a standard consultation fee of <b>two thousand shillings</b> only to be paid inside the health facility.
                                    </p>
                                </div>
                            </div>

                            <div class="faq">
                                <div class="question">
                                    <h3>Can I view past/upcoming appointments ?</h3>
                                
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"><path d="M12 17.414 3.293 8.707l1.414-1.414L12 14.586l7.293-7.293 1.414 1.414L12 17.414z"/></svg>
                                </div>
                                <div class="answer">
                                    <p>
                                        Yes, our doctor appointment management system allows you to easily view both past and upcoming appointments. Simply log in to your account, navigate to the appointments section, and you'll find a comprehensive list of all your past appointments as well as any upcoming ones. This feature helps you stay organized and keep track of your medical schedule effectively.
                                    </p>
                                </div>
                            </div>

                            <div class="faq">
                                <div class="question">
                                    <h3>Can I reschedule an appointment ?</h3>
                                
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"><path d="M12 17.414 3.293 8.707l1.414-1.414L12 14.586l7.293-7.293 1.414 1.414L12 17.414z"/></svg>
                                </div>
                                <div class="answer">
                                    <p>
                                        Absolutely, you can reschedule appointments through our doctor appointment management system. Simply log in to your account, navigate to the appointments section, and select the appointment you wish to reschedule. From there, you can choose a new time that better suits your schedule.<b> Please note that rescheduling an appointment can be done before 12 hours of the scheduling time.</b> Once you've made the changes, confirm the rescheduled appointment, and you'll receive updated confirmation details via email. This flexibility ensures that you can easily adjust your appointments as needed.
                                    </p>
                                </div>
                            </div>
                            
                        </div>
                    </div>
                </div>
            </div>
            
            
        </section>
        <!-- End of FAQ's section -->


        <?php
        function clean_input($string)
        {
            $string = trim($string);
            $string = stripslashes($string);
            $string = htmlspecialchars($string);
            return $string;
        }

        $name_ERR = "";
        $email_ERR = "";
        $message_ERR = "";

        if (isset($_POST['btn_submit'])) {
            $user_name = clean_input($_POST['user_name']);
            $email = clean_input($_POST['user_email']);
            $message = clean_input($_POST['user_message']);

            if (empty($user_name)) {
                $name_ERR = "* Name field is required";
            } elseif (!preg_match('/^[a-zA-Z]+(\s[a-zA-Z]+)+$/',$user_name)) {
                $name_ERR = "* Two names needed.";
            }

            if (empty($email)) {
                $email_ERR = "* Email is required";
            } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $email_ERR = "* Invalid email address";
            }

            if (empty($message)) {
                $message_ERR = "* Message is required";
            } elseif (!preg_match('/^[a-zA-Z\s!@#$%^&*(),.?":{}|<>]*$/',$message)) {
                $message_ERR = "* A valid message is required.";
            }


            if (empty($name_ERR) && empty($email_ERR) && empty($message_ERR)) {
                //send verification code using PhpMailer
                $mail = new PHPMailer(true);

                try {
                    //server settings
                    $mail->isSMTP();
                    $mail->Host       = 'smtp.gmail.com'; // SMTP server
                    $mail->SMTPAuth   = true;
                    $mail->Username   = 'markmulli001@gmail.com';
                    $mail->Password   = 'ruasjdwtqzvhzgnj';
                    $mail->SMTPSecure = 'tls';
                    $mail->Port       = 587;

                    //Recipients
                    $mail->setFrom($email,$email);
                    $mail->addAddress('markmulli001@gmail.com'); // recipient's email address

                    //Content
                    $mail->isHTML(true);
                    $mail->Subject = "CONTACT US SESSION WITH $user_name";
                    $mail->Body = $message;
                    if($mail->send()) {
                        $log_message = "$user_name with email $email contacted us";
                        logger($log_message);
                        echo '<script>
                            alert("Message sent successfully.");
                            </script>';
                    } else {
                        $log_message = "Error for this $user_name with email $email";
                        logger($log_message);
                    }
                } catch (Exception $e) {


                    echo '<script>
                             alert("Error sending the message. Please contact support.");
                            </script>';
                }
            }
        }
        ?>
        <!-- contact section -->
        <section id = "contact" class = "contact py">
            <div class = "container grid">
                <div class = "contact-left">
                    <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3988.5653148960387!2d36.96325607628262!3d-1.4359817358107771!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x182f7526047dbce5%3A0x436911ca69893c4a!2sAthi%20River%20Shalom%20Community%20Hospital!5e0!3m2!1sen!2ske!4v1715165714480!5m2!1sen!2ske" width="600" height="450" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
                </div>
                <div class = "contact-right text-white text-center bg-blue">
                    <div class = "contact-head">
                        <h3 class = "lead">Contact Us</h3>
                        <p class = "text text-md lead" style="margin-top: 1.5rem;">Feel free to communicate with us</p>
                    </div>
                    <form method="post">
                        <div class = "form-element">
                            <input type = "text" class = "form-control" placeholder="Your name" name="user_name">
                            <div class="error"><?php echo $name_ERR ?></div>
                        </div>
                        <div class = "form-element">
                            <input type = "text" class = "form-control" placeholder="Your email" name="user_email">
                            <div class="error"><?php echo $email_ERR ?></div>
                        </div>
                        <div class = "form-element">
                            <textarea rows = "5" placeholder="Your Message" class = "form-control" name="user_message"></textarea>
                            <div class="error"><?php echo $message_ERR ?></div>
                        </div>
                        <button type = "submit" class = "btn btn-book btn-submit" name="btn_submit">
                            <i class = "fas fa-arrow-right"></i> Send Message
                        </button>
                    </form>
                </div>
            </div>
        </section>
        <!-- end of contact section -->
    </main>

    <!-- footer  -->
    <footer id = "footer" class = "footer text-center">
        <div class = "container">
            <div class = "footer-inner text-white grid" style="padding: 5.5rem 0;">
                <div class = "footer-item">
                    <h3 class = "footer-head">about us</h3>
                    <div class = "icon">
                        DAMS
                    </div>
                    <p class = "text text-md">We boast a skilled team of doctor specialists that offer exceptional consultancies.</p>
                    <address>
                        <i class="fa fa-location-dot"></i> 505, Namanga Road, Athi River
                    </address>
                </div>

                <div class = "footer-item">
                    <h3 class = "footer-head">tags</h3>
                    <ul class = "tags-list flex">
                        <li>medication</li>
                        <li>emergency</li>
                        <li>therapy</li>
                        <li>surgery</li>
                    </ul>
                </div>

                <div class = "footer-item">
                    <h3 class = "footer-head">Quick Links</h3>
                    <ul>
                        <li><a href = "#" class = "text-white">Home</a></li>
                        <li><a href = "#FAQs" class = "text-white">FAQs</a></li>
                        <li><a href = "#contact" class = "text-white">Contact Us</a></li>
                        <li><a href = "schedules.php" class = "text-white">Appointment Schedule</a></li>
                    </ul>
                </div>

                <div class = "footer-item">
                    <h3 class = "footer-head">make an appointment</h3>
                    <p class = "text text-md">Schedule your appointment for any day.</p>
                    <ul class = "appointment-info">
                        <li>
                            <i class = "fas fa-clock"></i>
                            <span>8:00 AM - 05:00 PM</span>
                        </li>
                        <li>
                            <i class = "fas fa-envelope"></i>
                            <span><?php echo $admin_email?></span>
                        </li>
                        <li>
                            <i class = "fas fa-phone"></i>
                            <span><?php echo $contact_no?></span>
                        </li>
                    </ul>
                </div>
            </div>

            <div class="footer-links flex">
                    <ul class="social-links">
                        <li><a href="#" class="text-white flex"><i class="fab fa-facebook-f"></i></a></li>
                        <li><a href="#" class="text-white flex"><i class="fab fa-twitter"></i></a></li>
                        <li><a href="#" class="text-white flex"><i class="fab fa-linkedin"></i></a></li>
                    </ul>
                    <div class="copyright">Copyright © 2024 DAMS. All Rights Reserved.</div>
            </div>
        </div>
    </footer>
    <!-- end of footer  -->


    <!-- custom js -->
    <script src = "js/script.js"></script>
</body>
</html>