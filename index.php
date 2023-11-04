<?php

if (isset($_POST["submit"])) {
   $full_name = $_POST['full_name'];
   $email = $_POST['email'];
   $phone = $_POST['phone'];
   $passcode = bin2hex(random_bytes(5));

   if (empty($full_name) || empty($email) || empty($phone)) {
      die("Please fill in all fields.");
   }
   
   if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
      die("Invalid email format.");
   }
   
   $host = "127.0.0.1";
   $username = "root";
   $password = "";
   $database = "deewill";
   
   $mysqli = new mysqli($host, $username, $password, $database);
   
   if ($mysqli->connect_error) {
      die("Connection failed: " . $mysqli->connect_error);
   }

   $sql = "SELECT COUNT(*) FROM `wedding-registration` WHERE `email` = ?";

   $stmt = $mysqli->prepare($sql);
   $stmt->bind_param("s", $email);
   $stmt->execute();
   $stmt->bind_result($count);
   $stmt->fetch();
   $stmt->close();

   if ($count > 0) {
      $script = <<< JS
         alert("Email already exists. Please choose a different email.");
      JS;
   } else {
      $sql = "INSERT INTO `wedding-registration` (`full_name`, `email`, `phone`, `passcode`) VALUES (?, ?, ?, ?)";
      $insert_stmt = $mysqli->prepare($sql);
      $insert_stmt->bind_param("ssss", $full_name, $email, $phone, $passcode);
      
      if ($insert_stmt->execute()) {
         $script = <<< JS
         document.querySelector(".modal-").classList.toggle('toggle-modal');
         const button = document.querySelector("button"),
         toast = document.querySelector(".toast");
         (closeIcon = document.querySelector(".close")),
         (progress = document.querySelector(".progress"));

         let timer1, timer2;

         button.addEventListener("click", () => {
         toast.classList.add("active");
         progress.classList.add("active");

         timer1 = setTimeout(() => {
            toast.classList.remove("active");
         }, 5000); //1s = 1000 milliseconds

         timer2 = setTimeout(() => {
            progress.classList.remove("active");
         }, 5300);
         });

         toast.classList.add("active");
         progress.classList.add("active");

         timer1 = setTimeout(() => {
            toast.classList.remove("active");
         }, 15000); //1s = 1000 milliseconds

         timer2 = setTimeout(() => {
            progress.classList.remove("active");
         }, 15000);

         closeIcon.addEventListener("click", () => {
            toast.classList.remove("active");

            setTimeout(() => {
               progress.classList.remove("active");
            }, 3000);

            clearTimeout(timer1);
            clearTimeout(timer2);
         });
         JS;
      }
      $insert_stmt->close();
   }
}
?>

<html lang="en">
   <head>
      <!-- basic -->
      <meta charset="utf-8">
      <meta http-equiv="X-UA-Compatible" content="IE=edge">
      <!-- mobile metas -->
      <meta name="viewport" content="width=device-width, initial-scale=1">
      <meta name="viewport" content="initial-scale=1, maximum-scale=1">
      <!-- site metas -->
      <title>DeeWill23</title>
      <meta name="keywords" content="">
      <meta name="description" content="">
      <meta name="author" content="">
      <!-- bootstrap css -->
      <link rel="stylesheet" href="css/bootstrap.min.css">
      <!-- style css -->
      <link rel="stylesheet" href="css/style.css">
      <!-- Responsive-->
      <link rel="stylesheet" href="css/responsive.css">
      <!-- fevicon -->
      <link rel="icon" href="images/fevicon.png" type="image/gif" />
      <!-- Scrollbar Custom CSS -->
      <link rel="stylesheet" href="css/jquery.mCustomScrollbar.min.css">
      <!-- Tweaks for older IEs-->
      <link rel="stylesheet" href="https://netdna.bootstrapcdn.com/font-awesome/4.0.3/css/font-awesome.css">
      <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/fancybox/2.1.5/jquery.fancybox.min.css" media="screen">
      <link rel="stylesheet" href="https://rawgit.com/LeshikJanz/libraries/master/Bootstrap/baguetteBox.min.css">
      <!--[if lt IE 9]>
      <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
      <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script><![endif]-->
   </head>
   <!-- body -->
   <body class="main-layout">
      <!-- <div id="demo-modal" class="modal">
         <div class="modal__content">
            <h1>Reception Venue:</h1>

            <p>
               <strong>Passcode</strong>: 895h39h5339h
            </p>

            <div class="modal__foote mt-3">
               <strong>Address</strong>: City Park Abuja <i class="fa fa-heart"></i><br>
               Ahmadu Bello Way, Wuse 904101, Abuja, Federal Capital Territory, Nigeria
            </div>

            <a href="#" class="modal__close">&times;</a>
         </div>
      </div> -->
      <!-- Modal -->
      <div class="modal- toggle-modal" id="modal-one" aria-hidden="true">
         <div class="modal-dialog-">
            <div class="modal-header-">
               <h1>Reception Venue:</h1>
               <div class="btn-close cursor-pointer" aria-hidden="true" onClick="toggleModal()">×</div>
            </div>
            <div class="modal-body-">
               <div class="p-2 panel-tip">
               <i class="fa fa-info-circle mr-1 info" aria-hidden="true"></i>
               please copy the following details to safe place as it will be lost when you leave this page</div>
               <p class="mt-3"><strong class="passcode ">Passcode</strong>: <?php echo '<span class="deewill-info">' . $passcode . '</span>'; ?></p>
               <div class="mt-3">
                  <strong class="address">Address</strong>: City Park Abuja <i class="fa fa-heart address"></i><br>
                  Ahmadu Bello Way, Wuse 904101, Abuja, Federal Capital Territory, Nigeria
               </div>
            </div>
            <div class="modal-footer-"> 
               <button href="#modal-one" class="btn btn-primary mr-2" onClick="toggleMap()">See address</button>
               <button  class="btn btn-danger" onClick="toggleModal()">Cancel</button>
            </div>
         </div>
      </div>
      <!-- Map Start -->
      <div class="map hide-map">
         <button  class="btn btn-danger map-toggle" onClick="toggleMap()">Hide Map</button>
         <iframe src="https://www.google.com/maps/embed?pb=!1m26!1m12!1m3!1d4046971.639199464!2d2.8124954691234243!3d7.882434022338342!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!4m11!3e6!4m3!3m2!1d6.705370599999999!2d3.4296455999999997!4m5!1s0x104e0af9bbf0f9cd%3A0xda36fd8547df95af!2sCity%20Park%20Abuja%20%0D%0AAhmadu%20Bello%20Way%2C%20Wuse%20904101%2C%20Abuja%2C%20Federal%20Capital%20Territory%2C%20Nigeria!3m2!1d9.0760635!2d7.475778099999999!5e0!3m2!1sen!2sng!4v1699060631725!5m2!1sen!2sng" style="width: 100%; height: 100%"  style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>

      </div>
      <!-- Map End -->

      <!-- Success Toast Start -->
      <div class="toast">
         <div class="toast-content">
         <i class="fa fa-check check" aria-hidden="true"></i>

            <div class="message">
               <span class="text text-1">Success</span>
               <span class="text text-2">Your registration is  successful!</span>
            </div>
         </div>
         <i class="fa fa-times close" aria-hidden="true"></i>

         <!-- Remove 'active' class, this is just to show in Codepen thumbnail -->
         <div class="progress active"></div>
      </div>
      <!-- Success Toast End -->
      
      <!-- loader  -->
      <div class="loader_bg">
         <div class="loader"><img src="images/loading.gif" alt="#"/></div>
      </div>
      <!-- end loader -->
      <!-- top -->
      <div class="full_bg">
         <!-- header inner -->
         <div class="top_section">
            <div class="container-fluid">
               <div class="row">
                  <div class=" col-md-2 col-sm-3 col logo_section">
                     <div class="full">
                        <div class="center-desk">
                           <div class="logo">
                              <!-- <a href="index.html"><img src="images/deewill-logo.jpg" alt="#" /></a> -->
                           </div>
                        </div>
                     </div>
                  </div>
               </div>
            </div>
         </div>
         <!-- end header inner -->
         <!-- end header -->
         <div class="slider_main">
            <div class="container-fluid">
               <div class="row">
                  <div class="col-md-8">
                     <!-- carousel code -->
                     <div id="carouselExampleIndicators" class="carousel slide">
                        <ol class="carousel-indicators">
                           <li data-target="#carouselExampleIndicators" data-slide-to="0" class="active"></li>
                           <li data-target="#carouselExampleIndicators" data-slide-to="1"></li>
                           <li data-target="#carouselExampleIndicators" data-slide-to="2"></li>
                        </ol>
                        <div class="carousel-inner">
                           <!-- first slide -->
                           <div class="carousel-item active">
                              <!-- <div class="carousel-caption d-md-block cuplle">
                                 <h3 data-animation="text-black">
                                    Wilsson & Divine are getting married
                                 </h3>
                                 <a href="tel:+2348134207599" class="btn btn-primary  read_more whitebg mt-3" data-animation="animated bounceInLeft">Contact Us</a>
                              </div> -->
                           </div>
                        </div>
                        <!-- controls -->
                        <!-- <a class="carousel-control-prev" href="#carouselExampleIndicators" role="button" data-slide="prev">
                        <i class="fa fa-angle-left" aria-hidden="true"></i>
                        <span class="sr-only">Previous</span>
                        </a>
                        <a class="carousel-control-next" href="#carouselExampleIndicators" role="button" data-slide="next">
                        <i class="fa fa-angle-right" aria-hidden="true"></i>
                        <span class="sr-only">Next</span>
                        </a> -->
                     </div>
                  </div>
               </div>
            </div>
         </div>
      </div>
      <!-- end banner -->
      <div id="myHeader" class="header diki">
         <div class="container">
            <div class="row">
               <div class="col-md-10 col-sm-12">
                  <nav class="navigation navbar navbar-expand-md navbar-dark ">
                     <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarsExample04" aria-controls="navbarsExample04" aria-expanded="false" aria-label="Toggle navigation">
                     <span class="navbar-toggler-icon"></span>
                     </button>
                     <div class="collapse navbar-collapse" id="navbarsExample04">
                        <ul class="navbar-nav mr-auto">
                           <li class="nav-item active">
                              <a class="nav-link" href="index.html">Home</a>
                           </li>
                           
                           
                           <li class="nav-item">
                              <a class="nav-link" href="gallery.html">Gallery</a>
                           </li>
                           <li class="nav-item">
                              <a href="tel:+2348134207599" class="nav-link" data-animation="animated bounceInLeft">Contact Us</a>
                           </li>
                        </ul>
                     </div>
                  </nav>
               </div>
               <div class="col-md-2">
                  <div class="sealo">
                     <ul>
                        <li><a href="Javascript:void(0)"><i class="fa fa-search"></i></a></li>
                     </ul>
                  </div>
               </div>
            </div>
         </div>
      </div>
      <!-- about -->
      <div class="about">
         <div class="container">
            <div class="row">
               <div class="col-md-12">
                  <div class="titlepage text_align_center">
                     <h2>About Us</h2>
                  </div>
               </div>
               <div class="col-md-10 offset-md-1">
                  <div class="about_img text_align_center">
                     <figure><img src="images/deewill-1.jpg" alt="#"/></figure>
                  </div>
               </div>
               <div class="col-md-8 offset-md-2">
                  <div class="about_img text_align_center">
                     <p>The journey of Divine and Wilson started on a platform of service.
                        God has brought both of them thus far by faith and this the beginning of their journey to forever. 
                        
                        we are graciously elated you are part of this Joy.
                     </p>
                     <a href="tel:+2348134207599" class="read_more" data-animation="animated bounceInLeft">Reach us</a>
                  </div>
               </div>
            </div>
         </div>
      </div>
      <!-- end about -->
      <!-- couple -->
      <div class="couple">
         <div class="container-fluid">
            <div class="row">
               <div class="col-md-7">
                  <div class="titlepage text_align_left">
                     <span>Our introduction</span>
                     <h2>Meet Divine Partners </h2>
                     <p>The journey of Divine and Wilson started on a platform of service.
                        God has brought both of them thus far by faith and this the beginning of their journey to forever. 
                        
                        we are graciously elated you are part of this Joy.
                     </p>
                     <a class="read_more whitebg" href="tel:+2348134207599">Reach us</a>
                  </div>
               </div>
               <div class="col-md-5">
                  <div class="doi">
                     <figure><img src="images/deewill-1.jpg" alt="#"/></figure>
                  </div>
               </div>
            </div>
         </div>
      </div>
      <!-- couple -->
      <!-- gallery -->
      <div class="gallery">
         <div class="container">
            <div class="row">
               <div class="col-md-12">
                  <div class="titlepage">
                     <h2>Our Gallery</h2>
                  </div>
               </div>
            </div>
            <div class="tz-gallery">
               <div class="row">
                  <div class="col-md-4">
                     <div class="row">
                        <div class="col-md-12 ma_bottom30">
                           <div class="lightbox">
                              <img src="images/deewill-1.jpg" alt="Bridge">
                              <div class="view_main">
                                 <h3>Couple</h3>
                                 <a class="view_btn" href="images/deewill-1.jpg"><i class="fa fa-search-plus" aria-hidden="true"></i></a>
                              </div>
                           </div>
                        </div>
                        <div class="col-md-12 ma_bottom30">
                           <div class="lightbox">
                              <img src="images/deewill-1.jpg" alt="Bridge">
                              <div class="view_main">
                                 <h3>Couple</h3>
                                 <a class="view_btn" href="images/deewill-1.jpg"><i class="fa fa-search-plus" aria-hidden="true"></i></a>
                              </div>
                           </div>
                        </div>
                     </div>
                  </div>
                  <div class="col-md-4">
                     <div class="row">
                        <div class="col-md-12 ma_bottom30">
                           <div class="lightbox">
                              <img src="images/deewill-1.jpg" alt="Bridge">
                              <div class="view_main">
                                 <h3>Couple</h3>
                                 <a class="view_btn" href="images/deewill-1.jpg"><i class="fa fa-search-plus" aria-hidden="true"></i></a>
                              </div>
                           </div>
                        </div>
                        <div class="col-md-12 ma_bottom30">
                           <div class="lightbox">
                              <img src="images/deewill-1.jpg" alt="Bridge">
                              <div class="view_main">
                                 <h3>Couple</h3>
                                 <a class="view_btn" href="images/deewill-1.jpg"><i class="fa fa-search-plus" aria-hidden="true"></i></a>
                              </div>
                           </div>
                        </div>
                     </div>
                  </div>
                  <div class="col-md-4">
                     <div class="row">
                        <div class="col-md-12 ma_bottom30">
                           <div class="lightbox">
                              <img src="images/deewill-1.jpg" alt="Bridge">
                              <div class="view_main">
                                 <h3>Couple</h3>
                                 <a class="view_btn" href="images/deewill-1.jpg"><i class="fa fa-search-plus" aria-hidden="true"></i></a>
                              </div>
                           </div>
                        </div>
                        <div class="col-md-12 ma_bottom30">
                           <div class="lightbox">
                              <img src="images/deewill-1.jpg" alt="Bridge">
                              <div class="view_main">
                                 <h3>Couple</h3>
                                 <a class="view_btn" href="images/deewill-1.jpg"><i class="fa fa-search-plus" aria-hidden="true"></i></a>
                              </div>
                           </div>
                        </div>
                     </div>
                  </div>
                  <div class="col-md-12">
                     <p class="text_align_center">The journey of Divine and Wilson started on a platform of service. God has brought both of them thus far by faith and this the beginning of their journey to forever. we are graciously elated you are part of this Joy.
                     </p>
                  </div>
               </div>
            </div>
         </div>
      </div>
      <!-- end gallery -->
      <!-- contact -->
      <div class="contact">
         <div class="container-fluid">
            <div class="row ">
               <div class="col-md-12">
                  <div class="titlepage text_align_center">
                     <h2>kindly register to attend</h2>
                  </div>
               </div>
               <div class="col-md-6">
                  <div class="mapimg">
                     <figure><img src="images/map.png" alt="#"/></figure>
                  </div>
               </div>
               <div class="col-md-6">
                  <form id="request" class="main_form" method="POST">
                     <div class="row">
                        <div class="col-md-12 ">
                           <label>Full name</label>
                           <input class="contactus" placeholder="" type="type" name="full_name"> 
                        </div>
                        <div class="col-md-12">
                           <label>Phone Number</label>
                           <input class="contactus" placeholder="" type="type" name="phone">                          
                        </div>
                        <div class="col-md-12">
                           <label>Email</label>
                           <input class="contactus" placeholder="" type="type" name="email"> 
                        </div>
                        <!-- <div class="col-md-12">
                           <label>Message</label>
                           <textarea class="textarea" placeholder="" type="type" Message="Name"></textarea>
                        </div> -->
                        <div class="col-md-12">
                           <button class="send_btn" type="submit" name="submit">Register</button>
                        </div>
                     </div>
                  </form>
               </div>
            </div>
         </div>
      </div>
      <!-- contact -->
      <!--  footer -->
      <footer>
         <div class="footer">
            <div class="container">
               <div class="row justify-content-between border_bo1 ">
                  <div class="col-md-4">
                     <a class="logof" href="index.html"><img src="images/deewill-logo-2.jpg" alt="#"/></a> 
                     <!-- <form class="form_subscri">
                        <div class="row">
                           <div class="col-md-12">
                              <h3>Subscribe Now</h3>
                           </div>
                           <div class="col-md-12">
                              <input class="subsrib" placeholder="Enter your email" type="text" name="Enter your email">
                           </div>
                           <div class="col-md-12">
                              <button class="subsci_btn">subscribe</button>
                           </div>
                        </div>
                     </form> -->
                  </div>
                  <!-- <div class="col-lg-2 col-md-4 col-sm-6">
                     <div class="infoma">
                        <h3>Information</h3>
                        <ul>
                           <li>There are many </li>
                           <li>variations of pas</li>
                           <li>sages of Lorem  </li>
                           <li>psum available,  </li>
                           <li>but the majority  </li>
                           <li>have suffered  </li>
                        </ul>
                     </div>
                  </div>
                  <div class="col-lg-2 col-md-4 col-sm-6">
                     <div class="infoma">
                        <h3>Helpful Links</h3>
                        <ul>
                           <li>There are many </li>
                           <li>variations of pas</li>
                           <li>sages of Lorem  </li>
                           <li>psum available,  </li>
                           <li>but the majority  </li>
                        </ul>
                     </div>
                  </div>
                  <div class="col-lg-2 col-md-4 col-sm-6">
                     <div class="infoma">
                        <h3>Our Weddings</h3>
                        <ul>
                           <li>There are many </li>
                           <li>variations of pas</li>
                           <li>sages of Lorem  </li>
                           <li>psum available,  </li>
                           <li>but the majority  </li>
                        </ul>
                     </div>
                  </div> -->
                  <div class="col-lg-2 col-md-4 col-sm-6">
                     <div class="infoma">
                        <h3>Contact Us</h3>
                        <ul class="conta">
                           <li><i class="fa fa-map-marker" aria-hidden="true"></i>Locations 
                           </li>
                           <li><i class="fa fa-volume-control-phone" aria-hidden="true"></i>+2348134207599</li>
                           <li> <i class="fa fa-envelope" aria-hidden="true"></i><a href="Javascript:void(0)">uchewilson.wilson96@gmail.com</a></li>
                        </ul>
                        <!-- <ul class="social_icon">
                           <li><a href="Javascript:void(0)"><i class="fa fa-facebook" aria-hidden="true"></i></a></li>
                           <li><a href="Javascript:void(0)"><i class="fa fa-twitter" aria-hidden="true"></i></a></li>
                           <li><a href="Javascript:void(0)"><i class="fa fa-linkedin-square" aria-hidden="true"></i></a></li>
                           <li><a href="Javascript:void(0)"><i class="fa fa-instagram" aria-hidden="true"></i></a></li>
                        </ul> --> 
                     </div>
                  </div>
               </div>
            </div>
            <div class="copyright">
               <div class="container">
                  <div class="row">
                     <div class="col-md-12">
                        <p>© 2023 All Rights Reserved. <a href="#"> #DeeWill23</a></p>
                     </div>
                  </div>
               </div>
            </div>
         </div>
      </footer>
      <!-- end footer -->
      <!-- Javascript files-->
      <script src="js/jquery.min.js"></script>
      <script src="js/bootstrap.bundle.min.js"></script>
      <script src="js/jquery-3.0.0.min.js"></script>
      <!-- sidebar -->
      <script src="js/jquery.mCustomScrollbar.concat.min.js"></script>
      <script src="https://cdnjs.cloudflare.com/ajax/libs/baguettebox.js/1.8.1/baguetteBox.min.js"></script>
      <script src="js/custom.js"></script>
      <script type="text/javascript">
         baguetteBox.run('.tz-gallery');
      </script>
      <script type="text/javascript">
         window.onscroll = function() {myFunction()};
         
         var header = document.getElementById("myHeader");
         var sticky = header.offsetTop;
         
         function myFunction() {
          if (window.pageYOffset > sticky) {
            header.classList.add("sticky");
          } else {
            header.classList.remove("sticky");
          }
         }
      </script>
   </body>

   <script>
      const modal = document.querySelector(".modal-");
      
      function toggleModal() {
         const confirm = window.confirm("Are you sure you want to leave this page? You will loose access to this information when you leave this page")
         if(confirm) {
            modal.classList.toggle('toggle-modal')
         }
      }

      function toggleMap() {
         const map = document.querySelector(".map")
         map.classList.toggle("hide-map")
      }
   </script>
   <script><?= $script ?></script>
</html>