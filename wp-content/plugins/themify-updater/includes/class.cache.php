<?php

/**
 * Cache class for caching functionality.
 *
 * @since      1.1.4
 * @package    Themify_Updater_Cache
 * @author     Themify
 */
if ( !class_exists('Themify_Updater_Cache') ) :

class Themify_Updater_Cache {

	private $cache_type;
	private $cache;
	private $dbCacheKey = 'themify_updater_cache';

	function __construct ($type = "db") {

	    if ( defined('THEMIFY_UPDATER_DEBUG') && THEMIFY_UPDATER_DEBUG) {
	        $this->clear_all();
        }
		$this->cache_type = $type;
		
		if ($this->cache_type === 'session') {
			$this->load_session_cache();
		} elseif ($this->cache_type === 'db') {
			$this->load_db_cache();
		} elseif ($this->cache_type === 'hybrid') {
			$this->load_hybrid_cache();
		} else {
		    return;
        }

		$this->clean();
	}

	private function clear_all() {
        $this->start_session();

        if ( isset($_SESSION['themify_updater']) ) unset($_SESSION['themify_updater']);

        delete_option($this->dbCacheKey);
    }

    private function start_session() {
        if( session_status() != PHP_SESSION_DISABLED && !session_id() ) {
            @session_start();
        }
    }

	private function load_session_cache() {
	    $this->start_session();

		if (isset($_SESSION['themify_updater'])) {
            $this->cache['session'] = isset( $_SESSION['themify_updater']['s'] ) ? $_SESSION['themify_updater']['s'] : array();
		} else {
            $this->cache['session'] = array();
        }
	}

	private function load_db_cache() {
		$options = get_option( $this->dbCacheKey, array());
		$this->cache['db'] = isset($options['db']) ? $options['db'] : array();
	}

	private function update_cache() {
        if ( $this->cache_type === 'session' )
            $this->update_session_cache();
        else
            $this->update_db_cache();
    }

    private function update_session_cache() {
	    if ( isset($_SESSION['themify_updater']) )
	        $_SESSION['themify_updater']['s'] = $this->cache[ $this->cache_type ];
    }

    private function update_db_cache() {

        $this->clean();

        $options = get_option( $this->dbCacheKey, array());

        if ( $this->cache_type === 'hybrid' ) {
            $options['h'] = $this->cache[ $this->cache_type ];
            $_SESSION['themify_updater']['h']['ts'] = time() - 120;
        } elseif ( $this->cache_type === 'db' ) {
            $options['db'] =$this->cache[ $this->cache_type ];
        }

        delete_option( $this->dbCacheKey );
        add_option( $this->dbCacheKey, $options);
    }

	private function load_hybrid_cache() {
        $this->start_session();

		if ( empty( $_SESSION['themify_updater']['h'] ) || !isset( $_SESSION['themify_updater']['h']['ts'] ) || ( time() > (int) $_SESSION['themify_updater']['h']['ts'] ) ) {
            $options = get_option( $this->dbCacheKey, array());
            $_SESSION['themify_updater']['h']['items'] = isset($options['h']) ? $options['h'] : array();
            $_SESSION['themify_updater']['h']['ts'] = time() + 7200;
        }

        $this->cache['hybrid'] = $_SESSION['themify_updater']['h']['items'];
	}

	public function get($key) {
        if ( isset( $this->cache[ $this->cache_type ][$key] ) )
            return $this->cache[ $this->cache_type ][$key]['value'];
        else return false;
	}

	private function _set($key , $value, $time) {
	    $this->cache[ $this->cache_type ][ $key ] = array( 'value' => $value, 'expire' => $time);
	}

	public function set($key = false, $value, $time = 3600) {
		if(!$key) {
			$key = Themify_Updater_utils::get_hash( $value );
		}
		$time = time() + $time;

		$this->_set($key , $value, $time);

		$this->update_cache();

		return $key;
	}

	function remove( $key ) {
	    if ( isset( $this->cache[ $this->cache_type ][$key] ) )
	        unset( $this->cache[ $this->cache_type ][$key] );

	    $this->update_db_cache();
	}
	
	private function clean( $cache_type = '') {
		if ( empty($this->cache) ) return;

		if ( !empty($cache_type) ) {
            $this->_clean($cache_type);
        } else {
            foreach ( $this->cache as $type => $cache) {
                $this->_clean($type);
            }
        }
	}

    private function _clean( $type ) {
        foreach ( $this->cache[ $type ] as $key => $value ) {
            if ( time() > (int)$value['expire'] ) {
                unset($this->cache[$type][$key]);
            }
        }
    }
}
endif;
