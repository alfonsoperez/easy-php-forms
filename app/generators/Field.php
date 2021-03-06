<?php
namespace generators;


class Field {

    /*
    * Creates a HTML input text 
    */
    public function text($name, $default='', $placeholder='', $html5_attrs='')
    {
        return '<input type="text" name="'.$name.'" class="form-control" placeholder="'.$placeholder.'" value="'. $default .'" '.$html5_attrs.'/>';
    }

    public function email($name, $default='', $placeholder='', $html5_attrs='')
    {
        return '<input type="email" name="'.$name.'" class="form-control" placeholder="'.$placeholder.'" value="'.$default.'" '.$html5_attrs.'/>';
    }

    public function textarea($name, $default='', $placeholder='', $html5_attrs='')
    {
        return '<textarea class="form-control" name="'. $name .'">'. $default .'</textarea>';
    }

    public function checkbox($name, $checked=false, $placeholder='', $html5_attrs='')
    {
        $checked_attr = $checked ? 'checked="checked"' : '';
        return '<input class="form-control" type="checkbox" name="'. $name .'" '.$checked_attr.'/>';
    }

}