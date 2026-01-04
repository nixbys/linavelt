<!DOCTYPE html>
<html lang="en" class="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>GrapeJS Editor - FluxUI Dark Theme</title>
    
    <!-- Load GrapeJS CSS -->
    <link rel="stylesheet" href="https://unpkg.com/grapesjs/dist/css/grapes.min.css">
    <link rel="stylesheet" href="https://unpkg.com/grapesjs-preset-webpage/dist/grapesjs-preset-webpage.min.css">
    
    @vite(['resources/css/app.css', 'resources/css/grapesjs-dark-theme.css', 'resources/js/editor-app.js'])
    
    <style>
        body {
            margin: 0;
            padding: 0;
            overflow: hidden;
        }
        #gjs {
            border: none;
        }
    </style>
</head>
<body class="dark">
    <div id="gjs"></div>
</body>
</html>
