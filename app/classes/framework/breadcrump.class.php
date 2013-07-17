<?php

/**
 * Breadcrump o miga de pan para separar contenido
 *
 * @package Classes
 * @subpackage Breadcrump
 * @author Camilo Sperberg
 * @version 1.0
 */
class breadcrump {
    private $salida;
    private $contenido = array();
    public $separator = '&raquo;';
    public $num = 0;

    /**
     * Función que crea el breadcrump. Lo acompaña la frase introductoria
     *
     * @param $frase string La frase introductoria (ej: "Usted está en:") return bool Retorna siempre TRUE
     */
    public function __construct($frase = '') {
        if (!empty($frase)) {
            $this->salida = $frase;
        } else {
            $this->salida = _('You are at:&nbsp;');
        }
        $this->num = 0;
        return TRUE;
    }

    /**
     * Agregar contenido a la cola del breadcrump
     *
     * @param $link string El link hacia el contenido
     * @param $titulo string El título del contenido
     * @return bool Retorna TRUE en caso de haber sido agregado exitosamente, FALSE en caso contrario FIXME Revisar si
     * esta función quedó funcionando bien después de las últimas modificaciones!
     */
    public function add($link = '', $titulo = '') {
        $agregado = FALSE;
        if (!empty($titulo) and !in_array($link, $this->contenido)) {
            $this->contenido[] = array(
                $link,
                $titulo
            );
            $this->num++;
            $agregado = TRUE;
        }
        return $agregado;
    }

    /**
     * Función que compone el HTML del breadcrump y lo imprime y/o devuelve
     *
     * @param $echo bool Define si se imprime o no la cadena
     * @param $previo string HTML previo a todo el aparataje
     * @param $posterior string HTML posterior a todo el aparataje
     * @return string Retorna una cadena con el HTML
     */
    public function c_breadcrump($echo = FALSE, $previo = '', $posterior = '') {
        $resultado = FALSE;
        $i = 0;
        if (count($this->contenido) > 0) {
            $resultado = $previo . $this->salida;
            foreach ($this->contenido as $a) {
                if ($i != 0) {
                    $resultado .= '&nbsp;' . $this->separator . '&nbsp;';
                }
                $resultado .= '<a href="'.$a[0].'" title="'.sprintf(_('Click to see %s'), $a[1]).'">'.$a[1].'</a>';
                $i++;
            }
            $resultado .= $posterior;
            if ($echo) {
                echo $resultado;
            }
        }
        return $resultado;
    }
}
