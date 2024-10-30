<?php
/*
Plugin Name: Insert ShortCode Pattern
Plugin URI: http://3v-web.ru
Description: This plugin allows you to create templates and insert the specified text on the page using a short code. As a placement, you can specify the basement of the site, or in the place of insertion of a short code on the page. As a template, you can use any HTML tags (including javascript) without violating the integrity, as well as PHP code.
Version: 1.1.1
Author: Виталий
Author URI: http://3v-web.ru
License:     GPL2
License URI: https://www.gnu.org/licenses/gpl-2.0.html
Text Domain: insert-shortcode-pattern
Domain Path: /languages
*/

class InsertShortCodePattern
{
  public static $footer;
  public static $textDomain = 'insert-shortcode-pattern';
  
  public static function uploadDir () {
    return wp_upload_dir()['basedir'] . '/insert-shortcode-pattern/';
  }
  
  public static function searchKey ($atts) {
    
    $name = $atts['name'];

    if (empty($name)) {
        return __('The required «name» parameter is not passed', self::$textDomain);
    }
    
    $get_option = get_option('ins-shortcode-3v');
    if (!$get_option or !isset($get_option[$name])) {
        return sprintf(__('Key «%s» not found :(', self::$textDomain), $name);
    }
    $path = self::uploadDir () . $name . '.tpl';
    if (file_exists($path)) {
      ob_start();
      include ($path);
      $replace = ob_get_contents(); 
      ob_end_clean();
      if ($get_option[$name]['location'] == 2) {
        self::$footer .= $replace;
        $replace = false;
      }
      return $replace;
    }
    unset($get_option[$name]);
    update_option('ins-shortcode-3v', $get_option );
      return sprintf(__('Key «%s» not found :(', self::$textDomain), $name);
  }
  
  public static function insertFooter () {
    if (self::$footer) {
      echo "<!--Insert ShortCode Pattern-->\n".self::$footer."\n<!--#Insert ShortCode Pattern-->\n";
    }
  }
  
  public static function removeOption () {
    //удаления плагина
    delete_option('ins-shortcode-3v');
    //удаляем папку с шаблонами
    $dir = self::uploadDir ();
    $files = array_diff(scandir($dir), array('.','..'));
    foreach ($files as $file) {
      unlink($dir.'/'.$file);
    }
    return rmdir($dir); 
  }
  
  public static function showForm () {
    
    if (!current_user_can('activate_plugins')) {
      echo '
      <div class="settings-error error">
        <p>
          <strong>Ошибка:</strong> 
          У Вас недостаточно прав для внесения изменений. Необходима роль «Администратор»
        </p>
      </div>';
      return;
    }
    
    $get_option = get_option('ins-shortcode-3v');
    $name = (isset($_GET['name']) and $get_option) ? trim($_GET['name']) : false;
    $message = false;
    $code = false;
    $location = 0;
    
    if ($name) {
      if (isset ($get_option[$name])) {
        $path = self::uploadDir () . $name . '.tpl';
        if (file_exists($path)) {
          $code = file_get_contents($path);
          $location = $get_option[$name]['location'];
        } else {
          unset($get_option[$name]);
          update_option('ins-shortcode-3v', $get_option );
          $name = false;
        }
      } else {
        $name = false;
      }
    }
    $getName = $name;
    
    if (isset($_POST['on_submit'])) { 
      $error = false;
      $name = esc_html($_POST['shortcode']);
      $code = stripslashes($_POST['code']);
      $getName = esc_html($_POST['old_name']);
      $location = (int) $_POST['location'];
      if (!wp_verify_nonce($_POST['_wpnonce'], 'insert-shortcode')) {
        $error = __('Security check failed, please try again', self::$textDomain);
      }
      elseif (!preg_match('/^[a-z0-9-]+$/iu', $name)) {
        $error = __("Only English letters and numbers are allowed in the «Unique name» field", self::$textDomain);
      } elseif (isset($get_option[$name]) and $getName != $name) {
        $error = __("This name already exists, create a unique name", self::$textDomain );
      } else {
        $dir = self::uploadDir ();
        if (is_array($get_option)) {
          //update
          $get_option[$name] = array('location'=>$location);
          update_option('ins-shortcode-3v', $get_option );
        } else {
          //add
          $get_option = array( $name=>array('location'=>$location) );
          add_option('ins-shortcode-3v', $get_option, '', 'yes' );
          if (!file_exists($dir) and mkdir($dir, 0755)){
            copy(dirname( __FILE__ ).'/.htaccess', $dir.'.htaccess');
          }
        }
        if (file_put_contents ($dir . $name . '.tpl', $code)) {
          if ($getName and $getName != $name) {
            //удаляем старый фаил
            @unlink ($dir . $getName . '.tpl');
            unset($get_option[$getName]);
            update_option('ins-shortcode-3v', $get_option );
            $getName = $name;
          }
          $message = '<div id="setting-error-settings_updated" class="updated settings-error notice is-dismissible"><p><strong>'.__('Saved', self::$textDomain).'</strong></p></div>';
        } else {
          $error = __('Unable to write file, please try again', self::$textDomain);
        }
        
      }
      if ($error) {
        $message = '<div id="setting-error-settings_updated" class="updated settings-error notice is-dismissible error"><p><strong>'.__('Error', self::$textDomain).': '.$error.'</strong></p></div>';
      }
    }
    
    $list = '<a href="?page=ins-shortcode" class="button'.(empty($name) ? ' button-primary' : '').'">'.__('Add a new', self::$textDomain).'</a> ';
    if ($get_option) {
      foreach ($get_option as $key => $value) {
        $list .= '<a href="?page=ins-shortcode&name='.$key.'" class="button'.($key==$name ? ' button-primary' : '').'">'.$key.'</a> ';
      }
    }
    ?>
<div class="wrap">
  <h2>Insert ShortCode Pattern</h2>
  <?= $message ?>
  <p>
    <?= $list ?>
  </p>
  <div style="border: 1px solid;padding: 5px 15px;margin-bottom: 10px;">
    <form method="post" action="">
      <table class="form-table required_params">
        <tr>
          <th scope="row">
            <label><?= __('Unique name', self::$textDomain)?></label>
            <p class="description"><?= __('Only English letters, numbers and signs are allowed «-»', self::$textDomain)?></p>
          </th>
          <td>
            <input name="shortcode" value="<?= $name ?>" type="text" placeholder="<?= __('Specified name', self::$textDomain) ?>" onkeyup="nameChange(this)" required pattern="^[A-z0-9-]{1,}$" style="width:100%">
            <p class="description"><?= __('Short code to insert in the editor', self::$textDomain) ?>: <code>[InsertShortCodePattern name="<span><?= $name ?></span>"]</code> <strong style="color:red"></strong></p>
          </td>
        </tr>
        <tr>
          <th scope="row">
            <label><?= __('Location on page', self::$textDomain) ?>: </label>
          </th>
          <td>
            <select name="location">
              <option value="0"<?= $location == 0 ? ' selected' : '' ?>><?= __('At the insertion point of the short code', self::$textDomain) ?></option>
              <option value="2"<?= $location == 2 ? ' selected' : '' ?>><?= __('In footer site', self::$textDomain) ?></option>
            </select>
          </td>
        </tr>
        <tr>
          <th scope="row">
            <label><?= __('code', self::$textDomain) ?></label>
            <p class="description"><?= __('In this field you can insert any HTML and / or PHP code to display on Your site', self::$textDomain) ?></p>
          </th>
          <td>
            <textarea name="code" style="width: 100%; height: 200px" required placeholder="&lt;?= date('d.m.Y H:i') ?>"><?= $code ?></textarea>
          </td>
        </tr>
      </table>
      <input type="hidden" name="_wpnonce" value="<?= wp_create_nonce('insert-shortcode') ?>" />
      <input type="hidden" name="old_name" value="<?= $getName ? $getName : '' ?>" />
      <input type="hidden" name="on_submit" value="save" />
      <input type="submit" class="button button-primary" value="<?= $name ? __('Save', self::$textDomain) : __('Add', self::$textDomain) ?>" />
    </form>
  </div>
</div>
<script>
function nameChange(elem) {
  block = elem.parentNode;
  block.getElementsByTagName("span")[0].innerText = elem.value;
  block.getElementsByTagName("strong")[0].innerText = '<?= __('Save changes!!!', self::$textDomain) ?>';
}
</script>
<?php
  }
}

function insertShortCodeSearchKey ($atts) {
  return InsertShortCodePattern::searchKey ($atts);
}

function insertShortCodeAdminPage () {
  return InsertShortCodePattern::showForm ();
}

function insertShortCodeAdminMenu () {
    add_options_page('Insert ShortCode Pattern', 'Insert ShortCode Pattern', 'manage_options', 'ins-shortcode', 'insertShortCodeAdminPage');
}

add_action( 'plugins_loaded', 'true_load_plugin_textdomain' );

function true_load_plugin_textdomain() {
    load_plugin_textdomain( 'insert-shortcode-pattern', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
}

add_shortcode ('InsertShortCodePattern', 'insertShortCodeSearchKey');
add_action('wp_footer', array ('InsertShortCodePattern', 'insertFooter'), 100);
add_action('admin_menu', 'insertShortCodeAdminMenu');
register_uninstall_hook(__FILE__, array ('InsertShortCodePattern', 'removeOption'));