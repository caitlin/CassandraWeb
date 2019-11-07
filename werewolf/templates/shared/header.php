<html> 
  <head>
    <title><?php echo $page_title ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" type="text/css" href="/assets/css/application.css">
    <?php if ($theme != 'default') { echo "<link rel='stylesheet' type='text/css' href='/assets/css/themes/{$theme}.css'>"; } ?>
    <?php echo $page_scripts ?>
  </head>
  <body class="body">
    <?php display_menu() ?>
    <div class="content-wrap">
      <div class="content">