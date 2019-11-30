<?php
/*
Author: Dot-Rar <https://github.com/Dot-Rar>
Licensed under the MIT License
*/

if(!$user->isLoggedIn() || !$user->canViewACP($user->data()->id)) {
    header('Location: /');
    die();
} else if(!$user->isAdmLoggedIn()) {
    header('Location: /admin');
    die();
}
?>

<h3>Disweb NamelessMC Addon</h3>
<br />
<b>Author:</b> <a href="https://github.com/Dot-Rar">Dot-Rar</a>
<br />
<b>Version:</b> <?php echo DISWEB_VERSION ?>

<?php
$disweb_table = $queries->tableExists('disweb_settings');
if(empty($disweb_table)) {
    // Setup DB
    $queries->createTable('disweb_settings', "`server` VARCHAR(18) NOT NULL UNIQUE KEY, `channel` VARCHAR(18) NOT NULL UNIQUE KEY, `shard` VARCHAR(255) NOT NULL, PRIMARY KEY (`server`)", "ENGINE=InnoDB DEFAULT CHARSET=latin1");
    echo '<script>window.location.replace("/admin/addons/?action=edit&addon=disweb");</script>';
    die();
}

$server = "";
$channel = "";
$shard = "https://disweb.deploys.io";

$success = false;

if(Input::exists()) {
    if(Token::check(Input::get('token'))) {
        $validate = new Validate();
        $validation = $validate->check($_POST, array(
            'serverid' => array(
                'max' => 18
            ),
            'channelid' => array(
                'max' => 18
            ),
            'shard' => array(
                'max' => 225
            )
        ));

        if($validation->passed()) {
            $queries->delete('disweb_settings', array('server', '<>', '-1'));  // Don't ask me why I have to add those params, it won't work without
            $queries->create('disweb_settings', array(
                'server' => htmlspecialchars(Input::get('serverid')),
                'channel' => htmlspecialchars(Input::get('channelid')),
                'shard' => htmlspecialchars(Input::get('shard'))
            ));

            $server = htmlspecialchars(Input::get('serverid'));
            $channel = htmlspecialchars(Input::get('channelid'));
            $shard = htmlspecialchars(Input::get('shard'));

            $success = true;
        } else {
            echo '<div class="alert alert-danger">Your inputs were invalid</div>';
        }
    }
}

if(!$success) {
    // Get settings from DB
    $settings = $queries->getAll('disweb_settings', array("server", "<>", "0")); // Don't ask me why I have to add those params, it won't work without

    if(!empty($settings)) {
        $row = $settings[0];
        $server = htmlspecialchars($row->server);
        $channel = htmlspecialchars($row->channel);
        $shard = htmlspecialchars($row->shard);
    }
}
?>

<form role="form" action="" method="post">
    <div class="form-group">
        <label for="InputServerId">Server ID</label>
        <input type="text" id="InputServerId" name="serverid" class="form-control" placeholder="Server ID" value="<?php echo $server; ?>">
    </div>
    <div class="form-group">
        <label for="InputChannelId">Channel ID</label>
        <input type="text" id="InputChannelId" name="channelid" class="form-control" placeholder="Channel ID" value="<?php echo $channel; ?>">
    </div>
    <div class="form-group">
        <label for="InputShard">Shard</label>
        <input type="text" id="InputShard" name="shard" class="form-control" placeholder="https://disweb.deploys.io" value="<?php echo $shard; ?>">
    </div>

    <input type="hidden" name="token" value="<?php echo Token::generate(); ?>">
    <input type="submit" value="<?php echo $general_language['submit']; ?>" class="btn btn-primary">
</form>
