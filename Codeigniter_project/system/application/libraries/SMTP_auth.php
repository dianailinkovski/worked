<?php
/**
 * An SMTP object
 */
class SMTP_auth
{
  protected $data = array
  (
    'auth' => FALSE,
    'debug' => FALSE,
    'host' => 'localhost',
    'use_ssl' => FALSE,
    'use_tls' => FALSE,
    'password' => '',
    'persist' => '',
    'pipelining' => NULL,
    'port' => 25,
    'timeout' => NULL,
    'username' => ''
  );

  public function __construct(array $options = array())
  {
    if (isset($options['host'])) {
      $port = isset($options['port']) ? $options['port'] : $this->data['port'];
      $ssl = isset($options['use_ssl']) ? (boolean)$options['use_ssl'] : $this->data['use_ssl'];
      $tls = isset($options['use_tls']) ? (boolean)$options['use_tls'] : $this->data['use_tls'];
      $this->set_host($options['host'], $port, $ssl, $tls);
    }

    if (isset($options['username'])) {
      $password = isset($options['password']) ? $options['password'] : $this->data['password'];
      $this->authorize($options['username'], $password);
    }

    if (isset($options['timeout']))
      $this->set_timeout($options['timeout']);
  }

  /**
   * Enable SMTP authorization
   *
   * @param String $username
   * @param String $password
   */
  public function authorize($username, $password)
  {
    $this->data['auth'] = TRUE;
    $this->data['username'] = $username;
    $this->data['password'] = $password;
  }

  /**
   * Set the STMP host and port
   *
   * @param String $host
   * @param int $port
   */
  public function set_host($host, $port = 25, $ssl = FALSE, $tls = FALSE)
  {
    $this->data['host'] = $host;
    $this->data['port'] = ! empty($port) ? (int)$port : 25;
    $this->data['use_ssl'] = (boolean)$ssl;
    $this->data['use_tls'] = (boolean)$tls;
  }

  /**
   * Set the SMTP timeout
   *
   * @param int $timeout
   */
  public function set_timeout($timeout)
  {
    $this->data['timeout'] = ! empty($timeout) ? (int)$timeout : NULL;
  }

  /**
   * Get the SMTP parameters
   *
   * @return array
   */
  public function get($key = NULL)
  {
    if ( ! empty($key))
      return isset($this->data[$key]) ? $this->data[$key] : '';

    return $this->data;
  }
}