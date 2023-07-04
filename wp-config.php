<?php
/**
 * WordPress için taban ayar dosyası.
 *
 * Bu dosya şu ayarları içerir: MySQL ayarları, tablo öneki,
 * gizli anahtaralr ve ABSPATH. Daha fazla bilgi için 
 * {@link https://codex.wordpress.org/Editing_wp-config.php wp-config.php düzenleme}
 * yardım sayfasına göz atabilirsiniz. MySQL ayarlarınızı servis sağlayıcınızdan edinebilirsiniz.
 *
 * Bu dosya kurulum sırasında wp-config.php dosyasının oluşturulabilmesi için
 * kullanılır. İsterseniz bu dosyayı kopyalayıp, ismini "wp-config.php" olarak değiştirip,
 * değerleri girerek de kullanabilirsiniz.
 *
 * @package WordPress
 */

// ** MySQL ayarları - Bu bilgileri sunucunuzdan alabilirsiniz ** //
/** WordPress için kullanılacak veritabanının adı */
define('DB_NAME', 'boomgara_genew');

/** MySQL veritabanı kullanıcısı */
define('DB_USER', 'boomgara_genew');

/** MySQL veritabanı parolası */
define('DB_PASSWORD', 'gx{7@%JEWGfk');

/** MySQL sunucusu */
define('DB_HOST', 'localhost');

/** Yaratılacak tablolar için veritabanı karakter seti. */
define('DB_CHARSET', 'utf8mb4');

/** Veritabanı karşılaştırma tipi. Herhangi bir şüpheniz varsa bu değeri değiştirmeyin. */
define('DB_COLLATE', '');

/**#@+
 * Eşsiz doğrulama anahtarları.
 *
 * Her anahtar farklı bir karakter kümesi olmalı!
 * {@link http://api.wordpress.org/secret-key/1.1/salt WordPress.org secret-key service} servisini kullanarak yaratabilirsiniz.
 * Çerezleri geçersiz kılmak için istediğiniz zaman bu değerleri değiştirebilirsiniz. Bu tüm kullanıcıların tekrar giriş yapmasını gerektirecektir.
 *
 * @since 2.6.0
 */
define('AUTH_KEY',         '#>NSP|:!QN<bg1w<^K%LtP&kM;=tBbSi| tF/uGtZ|gxUX,&V R-,4cWBdc/AhD-');
define('SECURE_AUTH_KEY',  '$CMfSN%[uObLzfqf~pC_;^3?USDAzV[#4La)Dmq9-zEf7v0+R*Y_)B%eY.:eT}>6');
define('LOGGED_IN_KEY',    'NDt%1|&]8_Syk>1=d{{JV)u{eq*apcp0y,$go-F,pQ.s%g=-=,9GjL77D&c>-uUk');
define('NONCE_KEY',        'SrU zY-9(|tD},%L{K sUmuJzl)of4s(` -HDfJCxnmJ6lI*=k3D9w}Pu9&m*VUF');
define('AUTH_SALT',        'tgXO3k+9]tGzN+$i=E8.F.ifKwlO$T_Taf4pUFi{|vlK}Q+2^[_nc{Wk)z<a-FOZ');
define('SECURE_AUTH_SALT', '^(Ybaxhh +q<A6mxXdpKMAE--rG}dba}.4Tg|l>U/?3&|c1PQ&SY=!Vqdb=Ps+uW');
define('LOGGED_IN_SALT',   'Ih~Txq2_eV$$P]=6_6S14hW<-cmKB1:O2,9,K)4bowm[$Stpm6>8*,x428(2lSDn');
define('NONCE_SALT',       'nG-!Qnt{+a(L->:=|U<}Is+U{B$Pk]-8xw[]4Og [(_Gk0l9~K,3sCjg|&D%z^IW');
/**#@-*/

/**
 * WordPress veritabanı tablo ön eki.
 *
 * Tüm kurulumlara ayrı bir önek vererek bir veritabanına birden fazla kurulum yapabilirsiniz.
 * Sadece rakamlar, harfler ve alt çizgi lütfen.
 */
$table_prefix  = 'wp_';

/**
 * Geliştiriciler için: WordPress hata ayıklama modu.
 *
 * Bu değeri "true" yaparak geliştirme sırasında hataların ekrana basılmasını sağlayabilirsiniz.
 * Tema ve eklenti geliştiricilerinin geliştirme aşamasında WP_DEBUG
 * kullanmalarını önemle tavsiye ederiz.
 */
define('WP_DEBUG', false);

/* Hepsi bu kadar. Mutlu bloglamalar! */

/** WordPress dizini için mutlak yol. */
if ( !defined('ABSPATH') )
	define('ABSPATH', dirname(__FILE__) . '/');

/** WordPress değişkenlerini ve yollarını kurar. */
require_once(ABSPATH . 'wp-settings.php');
