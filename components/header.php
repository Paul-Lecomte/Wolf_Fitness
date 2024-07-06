<?php
session_start();
?>

<html lang="en">
  <head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Login Wolf Fitness</title>
    <link rel="stylesheet" href="../../node_modules/bulma/css/bulma.css">
    <link rel="stylesheet" href="../../stylesheet/style.css">
    <style>
      #footer {
        position: fixed;
        bottom: 0;
        left: 0;
        width: 100%;
        transition: transform 0.3s ease-in-out;
      }

      #footer.hidden {
        transform: translateY(100%);
      }

      .loading-page {
        position: fixed;
        z-index: 9999;
        background: grey;
        height: 100%;
        width: 100%;
        display: flex;
        flex-direction: column;
        gap: 1.5rem;
        align-items: center;
        justify-content: center;
        color: #191654;
      }
    </style>
  </head>
  <body class="site">
    <main class="m-0">