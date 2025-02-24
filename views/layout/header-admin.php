<?php
if(!defined('_CODE')){
    die('Access denied...');
}
?>



<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="<?php echo _WEB_HOST_TEMPLATES; ?>/css/style.css?ver=<?php echo rand();?>">
    <link rel="stylesheet" href="<?php echo _WEB_HOST_TEMPLATES; ?>/css/header.css?ver=<?php echo rand();?>">
    <link rel="stylesheet" href="<?php echo _WEB_HOST_TEMPLATES; ?>/css/slide-bar-menu.css?ver=<?php echo rand();?>">
    <link rel="stylesheet" href="<?php echo _WEB_HOST_TEMPLATES; ?>/css/table.css?ver=<?php echo rand();?>">
    <title><?php echo !empty($title['pageTitle']) ? $title['pageTitle'] : 'Mangaer User'; ?></title>
</head>
<body>
<header class="header">
            <nav>
                <div id="header-menu">
                      <label class="hamburger" for="check-icon">
                          <input type="checkbox" id="check-icon">
                          <svg viewBox="0 0 32 32">
                              <path d="M27 10 L13 10 C10.8 10 9 8.2 9 6 9 3.5 10.8 2 13 2 15.2 2 17 3.8 17 6 L17 26 C17 28.2 18.8 30 21 30 23.2 30 25 28.2 25 26 25 23.8 23.2 22 21 22 L7 22" class="line line-top-bottom" />
                              <path d="M7 16 L27 16" class="line"/>
                          </svg>
                      </label>
                      <form action="sreach">
                        <div class="group">
                          <svg class="icon" aria-hidden="true" viewBox="0 0 24 24">
                              <g>
                                  <path d="M21.53 20.47l-3.66-3.66C19.195 15.24 20 13.214 20 11c0-4.97-4.03-9-9-9s-9 4.03-9 9 4.03 9 9 9c2.215 0 4.24-.804 5.808-2.13l3.66 3.66c.147.146.34.22.53.22s.385-.073.53-.22c.295-.293.295-.767.002-1.06zM3.5 11c0-4.135 3.365-7.5 7.5-7.5s7.5 3.365 7.5 7.5-3.365 7.5-7.5 7.5-7.5-3.365-7.5-7.5z"></path>
                              </g>
                          </svg>
                          <input placeholder="Search" type="search" class="input">
                      </div>
                      </form>
                      <div class="right-header">
                        <div class="notification">
                            <li>
                              <svg width="25" height="25" viewBox="0 0 25 25" fill="none" xmlns="http://www.w3.org/2000/svg">
                                  <g clip-path="url(#clip0_2_2658)">
                                    <path fill-rule="evenodd" clip-rule="evenodd" d="M21.3692 8.01423L20.7467 6.93414C20.2202 6.02022 19.0532 5.70494 18.138 6.22934C17.7024 6.48596 17.1826 6.55877 16.6932 6.43171C16.2038 6.30464 15.7851 5.98814 15.5293 5.55199C15.3648 5.27477 15.2764 4.95901 15.273 4.63666C15.2879 4.11984 15.0929 3.61902 14.7325 3.24829C14.3721 2.87756 13.877 2.66848 13.36 2.6687H12.106C11.5995 2.66869 11.1138 2.87053 10.7565 3.22956C10.3992 3.5886 10.1997 4.07521 10.2021 4.58174C10.1871 5.62754 9.33498 6.46743 8.28907 6.46732C7.96671 6.46397 7.65096 6.37556 7.37373 6.21103C6.45856 5.68663 5.29161 6.00191 4.76504 6.91584L4.09685 8.01423C3.57091 8.92701 3.8819 10.0932 4.7925 10.623C5.3844 10.9647 5.74903 11.5963 5.74903 12.2797C5.74903 12.9632 5.3844 13.5947 4.7925 13.9365C3.88306 14.4626 3.57173 15.626 4.09685 16.536L4.72843 17.6253C4.97515 18.0705 5.3891 18.399 5.87869 18.5381C6.36828 18.6773 6.89314 18.6156 7.33712 18.3667C7.77358 18.112 8.29369 18.0422 8.78184 18.1729C9.26999 18.3035 9.68574 18.6237 9.93666 19.0623C10.1012 19.3396 10.1896 19.6553 10.193 19.9777C10.193 21.0342 11.0495 21.8907 12.106 21.8907H13.36C14.413 21.8907 15.268 21.0398 15.273 19.9868C15.2706 19.4787 15.4714 18.9907 15.8307 18.6314C16.19 18.2721 16.678 18.0713 17.1861 18.0738C17.5077 18.0824 17.8221 18.1704 18.1014 18.3301C19.0142 18.856 20.1804 18.545 20.7101 17.6344L21.3692 16.536C21.6243 16.0982 21.6943 15.5767 21.5637 15.087C21.4331 14.5974 21.1127 14.18 20.6735 13.9273C20.2343 13.6746 19.9139 13.2572 19.7833 12.7676C19.6527 12.278 19.7228 11.7565 19.9779 11.3186C20.1437 11.029 20.3839 10.7889 20.6735 10.623C21.5786 10.0935 21.8889 8.93411 21.3692 8.02339V8.01423Z" stroke="white" stroke-linecap="round" stroke-linejoin="round"/>
                                    <path d="M12.7377 14.9159C14.1936 14.9159 15.3739 13.7356 15.3739 12.2797C15.3739 10.8238 14.1936 9.64355 12.7377 9.64355C11.2818 9.64355 10.1016 10.8238 10.1016 12.2797C10.1016 13.7356 11.2818 14.9159 12.7377 14.9159Z" stroke="white" stroke-linecap="round" stroke-linejoin="round"/>
                                    </g>
                                    <defs>
                                    <clipPath id="clip0_2_2658">
                                    <rect width="24" height="24" fill="white" transform="translate(0.5625 0.390625)"/>
                                    </clipPath>
                                    </defs>
                              </svg>
                            </li>
                            <li>
                              <svg width="25" height="25" viewBox="0 0 25 25" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <g clip-path="url(#clip0_2_2662)">
                                <path d="M18.3438 8.39062C18.3438 6.79932 17.7116 5.2732 16.5863 4.14798C15.4611 3.02277 13.9351 2.39062 12.3438 2.39062C10.7524 2.39062 9.22633 3.02277 8.10111 4.14798C6.97589 5.2732 6.34375 6.79932 6.34375 8.39062C6.34375 15.3906 3.34375 17.3906 3.34375 17.3906H21.3438C21.3438 17.3906 18.3438 15.3906 18.3438 8.39062Z" stroke="white" stroke-linecap="round" stroke-linejoin="round"/>
                                <path d="M14.0738 21.3906C13.898 21.6937 13.6457 21.9453 13.342 22.1201C13.0384 22.295 12.6942 22.3871 12.3438 22.3871C11.9934 22.3871 11.6492 22.295 11.3456 22.1201C11.042 21.9453 10.7896 21.6937 10.6138 21.3906" stroke="white" stroke-linecap="round" stroke-linejoin="round"/>
                                </g>
                                <defs>
                                <clipPath id="clip0_2_2662">
                                <rect width="24" height="24" fill="white" transform="translate(0.34375 0.390625)"/>
                                </clipPath>
                                </defs>
                              </svg>
                            </li>
                            <li>
                              <svg width="21" height="23" viewBox="0 0 21 23" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <g clip-path="url(#clip0_2_2666)">
                                <path d="M15.4909 8.52808L11.4516 11.8127C10.6884 12.4181 9.61468 12.4181 8.85151 11.8127L4.77808 8.52808" stroke="white" stroke-width="1.36364" stroke-linecap="round" stroke-linejoin="round"/>
                                <path fill-rule="evenodd" clip-rule="evenodd" d="M14.5877 19.5724C17.3525 19.58 19.216 17.3083 19.216 14.5164V8.27239C19.216 5.4804 17.3525 3.20874 14.5877 3.20874H5.66249C2.89762 3.20874 1.03418 5.4804 1.03418 8.27239V14.5164C1.03418 17.3083 2.89762 19.58 5.66249 19.5724H14.5877Z" stroke="white" stroke-width="1.36364" stroke-linecap="round" stroke-linejoin="round"/>
                                </g>
                                <defs>
                                <clipPath id="clip0_2_2666">
                                <rect width="20" height="22" fill="white" transform="translate(0.125 0.390625)"/>
                                </clipPath>
                                </defs>
                                </svg>
                            </li>
                        </div>
                        <div class="logout">
                            <button ><a href="?module=auth&action=logout">Sign out</a></button>
                        </div>
                        <div class="user-information">
                            <div class="avata-user">
                              <img src="https://i0.wp.com/unatotovietnam.com/wp-content/uploads/2023/02/con-duong-hoa-anh-dao-nhat-ban-dep_102504656.jpeg?resize=1020%2C765&ssl=1" alt="">
                            </div>
                            <div class="information-data">
                                <div class="name-user">
                                  <span>Thomas Fleming</span>
                                </div>
                                <div class="email-user">
                                  <span>info@gmail.com</span>
                                </div>
                            </div>
                        </div>
                      </div>
                </div>
            </nav>
</header>
        <div class="container position_ct">
            <div class="nav-slider" id="menu-slide">
            <div class="contai-slibar">
                <div class="logo">
                    <h1>MAKI</h1>
                </div>
                <div class="menu-nav">
                <div class="yourcompany">
                    <div class="title-menu">YOUR COMPANY</div>
                    <div class="dropdown-menu">
                        <button class="button-menu-body-item">
                        <a href="#" class="button-menu-slidebar">
                            <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M2.5 7.50008L10 1.66675L17.5 7.50008V16.6668C17.5 17.1088 17.3244 17.5327 17.0118 17.8453C16.6993 18.1578 16.2754 18.3334 15.8333 18.3334H4.16667C3.72464 18.3334 3.30072 18.1578 2.98816 17.8453C2.67559 17.5327 2.5 17.1088 2.5 16.6668V7.50008Z" stroke="#888888" stroke-linecap="round" stroke-linejoin="round"/>
                            <path d="M7.5 18.3333V10H12.5V18.3333" stroke="#888888" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                            <span>Dashboard</span>
                        </a>
                        <div class="star"></div>
                        </button>
                        <div class="dropdown-content" id="dropdown-content-1">
                        <a a href="?module=user&action=list_user"> 
                            <i class="fixe"></i>
                            <span>User List</span>
                        </a>
                        <a href="?module=manga&action=list_manga"> 
                            <i class="fixe"></i>
                            <span>Manga</span>
                        </a>
                        </div>
                        
                    </div>
                    <!-- END DASHBOARD -->
                    <div class="dropdown-menu">
                    <button class="button-menu-body-item">
                        <a href="#" class="button-menu-slidebar">
                        <svg width="22" height="23" viewBox="0 0 22 23" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <g clip-path="url(#clip0_2_2707)">
                            <path fill-rule="evenodd" clip-rule="evenodd" d="M10.986 14.5674C7.4407 14.5674 4.41309 15.1035 4.41309 17.2502C4.41309 19.397 7.4215 19.9522 10.986 19.9522C14.5313 19.9522 17.5581 19.4153 17.5581 17.2694C17.5581 15.1235 14.5505 14.5674 10.986 14.5674Z" stroke="#888888" stroke-linecap="round" stroke-linejoin="round"/>
                            <path fill-rule="evenodd" clip-rule="evenodd" d="M10.986 11.5053C13.3126 11.5053 15.1983 9.61871 15.1983 7.29213C15.1983 4.96554 13.3126 3.07983 10.986 3.07983C8.65944 3.07983 6.77285 4.96554 6.77285 7.29213C6.76499 9.61086 8.63849 11.4974 10.9563 11.5053H10.986Z" stroke="#888888" stroke-linecap="round" stroke-linejoin="round"/>
                            </g>
                            <defs>
                            <clipPath id="clip0_2_2707">
                            <rect width="22" height="22" fill="white" transform="translate(0 0.5)"/>
                            </clipPath>
                            </defs>
                        </svg>
                        <span>Employees</span>
                        </a>
                    </button>
                    <div class="dropdown-content" id="dropdown-content-1"></div>
                </div>
                    <!-- END Employees -->
                

                <div class="dropdown-menu">
                    <button class="button-menu-body-item">
                    <a href="#" class="button-menu-slidebar">
                        <svg width="22" height="23" viewBox="0 0 22 23" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <g clip-path="url(#clip0_2_2734)">
                        <path fill-rule="evenodd" clip-rule="evenodd" d="M14.9732 2.9585H7.0266C4.25735 2.9585 2.52118 4.91925 2.52118 7.69399V15.1813C2.52118 17.9561 4.2491 19.9168 7.0266 19.9168H14.9723C17.7507 19.9168 19.4795 17.9561 19.4795 15.1813V7.69399C19.4795 4.91925 17.7507 2.9585 14.9732 2.9585Z" stroke="#888888" stroke-linecap="round" stroke-linejoin="round"/>
                        <path d="M7.73657 11.4377L9.91274 13.6129L14.2632 9.26245" stroke="#888888" stroke-linecap="round" stroke-linejoin="round"/>
                        </g>
                        <defs>
                        <clipPath id="clip0_2_2734">
                        <rect width="22" height="22" fill="white" transform="translate(0 0.4375)"/>
                        </clipPath>
                        </defs>
                        </svg>
                        
                        
                        <span>Performance</span>
                    </a>
                    </button>
                    <div class="dropdown-content" id="dropdown-content-1"></div>
                </div>
                <!-- END Performance -->


                <div class="helpdesk">
                    <button>Help Desk</button>
                </div>
                </div>
                <!-- END Outfeature -->
                </div>
            </div>
            </div>

