<html>
<head>
<title>Printing Documents</title>
<link rel="stylesheet" href="<?php _e(base_url()) ?>css/print.css" type="text/css" media="print"/>
<link rel="stylesheet" href="<?php _e(base_url()) ?>css/print.css" type="text/css" media="screen"/>
</head>
<body>
<div id="container">
    <div id="header">
        <!-- <img src="<?php _e(base_url()) ?>css/images/logo_mode.png" /> -->   
        <h2><?php echo config_item('shop_name')?></h2>
        <p>
            Alamat: <br />
            <?php echo config_item('shop_address')?><br />            
            Telp. <?php echo config_item('shop_phone')?>
        </p>                  
    </div>
    <div id="content">
        <?php _e($content)?></td>
    </div>
</div>
</body>
</html>