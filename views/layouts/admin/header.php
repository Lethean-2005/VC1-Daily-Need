<!DOCTYPE html>
<html lang="en">
    <!-- [Head] Start -->
  <head>
    <title>Daily Needs Dashboard</title>
    <!-- [Meta] -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimal-ui">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="description" content="Mantis is made using Bootstrap 5 design framework. Download the free admin template & use it for your project.">
    <meta name="keywords" content="Mantis, Dashboard UI Kit, Bootstrap 5, Admin Template, Admin Dashboard, CRM, CMS, Bootstrap Admin Template">
    <meta name="author" content="CodedThemes">

    <!-- [Favicon] icon -->
    <link rel="icon" href="/assets/images/favicon-32.png?v=3" type="image/png">
    <link rel="icon" href="/assets/images/favicon.svg?v=3" type="image/svg+xml">
    <link rel="apple-touch-icon" href="/assets/images/favicon-180.png?v=3">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Nunito:wght@300;400;500;600;700&family=Kantumruy+Pro:wght@300;400;500;600;700&display=swap" id="main-font-link">
    <!-- [Tabler Icons] https://tablericons.com -->
    <link rel="stylesheet" href="/assets/fonts/tabler-icons.min.css" >
    <!-- [Feather Icons] https://feathericons.com -->
    <link rel="stylesheet" href="/assets/fonts/feather.css" >
    <!-- [Font Awesome Icons] https://fontawesome.com/icons -->
    <link rel="stylesheet" href="/assets/fonts/fontawesome.css" >
    <!-- [Material Icons] https://fonts.google.com/icons -->
    <link rel="stylesheet" href="/assets/fonts/material.css" >
    <!-- [Template CSS Files] -->
    <link rel="stylesheet" href="/assets/css/style.css" id="main-style-link" >
    <link rel="stylesheet" href="/assets/css/style-preset.css" >
    <!-- link bootstrab -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css">
    <link rel="stylesheet" href="/assets/css/plugins/register.css">
    <style>
      .logo-lg{
        width: 55%;
      }
      .b-brand{
        display: flex;
        align-items: center;
      }
      .dashboard{
        margin-top: 10% !important;
      }
      .daily{
        width: 2rem;
        height: 2rem;
        border-radius: 50%;
        margin-right: 0.5rem;
      }
      /* nav content */
      .navbar-content{
        background-color: #D9D9D9;
      }

      /*  cards products */
      .product-card {
            border: 2px solid transparent;
            border-radius: 10px;
            padding: 15px;
            transition: 0.3s;
            background: #f8f9fa; /* Light background */
        }

        .product-card:hover {
            border-color: #007bff; /* Blue border on hover */
        }

        .product-image {
            background: #eef5ff; /* Light blue background */
            border-radius: 10px;
            padding: 15px;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 180px; /* Fixed height */
        }

        .product-title {
            font-weight: bold;
        }

        .product-category {
            color: #6c757d;
            font-size: 14px;
        }

        .product-price {
            color: #28a745;
            font-weight: bold;
            font-size: 18px;
        }

        .rating {
            color: gold;
            font-size: 14px;
        }

        .btn-custom {
            font-size: 14px;
            width: 100%;
        }

        .edit-btn, .delete-btn {
            width: 100px; /* Equal button width */
        }

        .product-details {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .buttons {
            display: flex;
            flex-direction: column;
            align-items: flex-end;
        }

        .buttons button {
            margin-bottom: 5px; /* Space between buttons */
        }
    </style>

  </head>
  <!-- [Head] end -->
  <!-- [Body] Start -->

  <body data-pc-preset="preset-1" data-pc-direction="ltr" data-pc-theme="light">