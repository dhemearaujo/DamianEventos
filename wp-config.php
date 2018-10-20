<?php
/**
 * As configurações básicas do WordPress
 *
 * O script de criação wp-config.php usa esse arquivo durante a instalação.
 * Você não precisa usar o site, você pode copiar este arquivo
 * para "wp-config.php" e preencher os valores.
 *
 * Este arquivo contém as seguintes configurações:
 *
 * * Configurações do MySQL
 * * Chaves secretas
 * * Prefixo do banco de dados
 * * ABSPATH
 *
 * @link https://codex.wordpress.org/pt-br:Editando_wp-config.php
 *
 * @package WordPress
 */

// ** Configurações do MySQL - Você pode pegar estas informações com o serviço de hospedagem ** //
/** O nome do banco de dados do WordPress */
define('DB_NAME', 'damian');

/** Usuário do banco de dados MySQL */
define('DB_USER', 'root');

/** Senha do banco de dados MySQL */
define('DB_PASSWORD', '');

/** Nome do host do MySQL */
define('DB_HOST', 'localhost');

/** Charset do banco de dados a ser usado na criação das tabelas. */
define('DB_CHARSET', 'utf8mb4');

/** O tipo de Collate do banco de dados. Não altere isso se tiver dúvidas. */
define('DB_COLLATE', '');

/**#@+
 * Chaves únicas de autenticação e salts.
 *
 * Altere cada chave para um frase única!
 * Você pode gerá-las
 * usando o {@link https://api.wordpress.org/secret-key/1.1/salt/ WordPress.org
 * secret-key service}
 * Você pode alterá-las a qualquer momento para invalidar quaisquer
 * cookies existentes. Isto irá forçar todos os
 * usuários a fazerem login novamente.
 *
 * @since 2.6.0
 */
define('AUTH_KEY',         'D;(dV##3R!<kp-8#cRGB<QcOHLy_(f16sH&IPcJ.I1d>ZMQVy1L_hlQs8!JrY>?3');
define('SECURE_AUTH_KEY',  ']iO_?sIdx=Ypo:1fO c.OOQ#@g^a(KA1Q844;vrYzH5m}fq-_du,8KWg)/vmWlw)');
define('LOGGED_IN_KEY',    '1h,>Z|Im>SknD78:OpxOa.iWHg/~v[ceE*Q&}^*sUp57@|d*>b|D3Mg]o,i5nK]Z');
define('NONCE_KEY',        'G^Zk E=d)W 9sF&Kv3)T`.%Qq~ddWNh!=$4nTM;2Nhdo8}%!EO;^K)u)zvz/1-&R');
define('AUTH_SALT',        'N+o6<q0nI|M.FlKHvaja$H@2QhD:/$Dq$edyHv#?{E*B$[;+yUl{/0%sJnK{/^E4');
define('SECURE_AUTH_SALT', '}MU-IJ-rQIZr2(l$,G@u;b[)L8v*7&mW5Gv+HA;Kg|4$erw URj*I1R!l?-}7?JO');
define('LOGGED_IN_SALT',   'N{qd ^_mxO#h<yJ$,@N|#3W= gv6$1B G?d`H!,COZLCYlaQuT1} 8JG8pX45G{w');
define('NONCE_SALT',       'tuX)?#l2@SLXO~5YQ0n=?nCPEJaw%I:*=1LlAX8.KXXaz[:*|^>UjHg}kH&+=B~g');

/**#@-*/

/**
 * Prefixo da tabela do banco de dados do WordPress.
 *
 * Você pode ter várias instalações em um único banco de dados se você der
 * um prefixo único para cada um. Somente números, letras e sublinhados!
 */
$table_prefix  = 'wp_';

/**
 * Para desenvolvedores: Modo de debug do WordPress.
 *
 * Altere isto para true para ativar a exibição de avisos
 * durante o desenvolvimento. É altamente recomendável que os
 * desenvolvedores de plugins e temas usem o WP_DEBUG
 * em seus ambientes de desenvolvimento.
 *
 * Para informações sobre outras constantes que podem ser utilizadas
 * para depuração, visite o Codex.
 *
 * @link https://codex.wordpress.org/pt-br:Depura%C3%A7%C3%A3o_no_WordPress
 */
define('WP_DEBUG', false);

/* Isto é tudo, pode parar de editar! :) */

/** Caminho absoluto para o diretório WordPress. */
if ( !defined('ABSPATH') )
	define('ABSPATH', dirname(__FILE__) . '/');

/** Configura as variáveis e arquivos do WordPress. */
require_once(ABSPATH . 'wp-settings.php');
