<?php

/**
 * Form creator class
 * 
 * @author Robin Migalski (RobinMglsk) <robin@mglsk.be>
 * @license MIT
 * @note This program is distributed in the hope that it will be useful - WITHOUT
 * ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or
 * FITNESS FOR A PARTICULAR PURPOSE.
 */

Namespace RobinMglsk\PHPFormCreator;

class FormCreator {

    private $formData = null;
    private $formHTML = null;
    private $language = 'en';

    private $styles = [
        'form-group' => 'form-group',
        'form-control' => 'form-control',
        'form-check' => 'form-check',
        'form-check-input' => 'form-check-input',
        'form-check-label' => 'form-check-label',
        'col' => 'col',
        'button-classes' => 'btn btn-primary mt-3',
    ];

    private $container = '{FORM}';

    /**
     * Create formCreator object
     * 
     * @param object $data - Form data
     */
    public function __construct($data, $language = 'en'){
        $this->formData = $data;
        $this->language = $language;
        $this->container = '<div class="row">{FORM}</div>';
    }

    /**
     * Render HTML
     */
    public function getFormHTML(){

        foreach($this->formData as $key => $item){
            
            switch ($item->type) {

                case 'input':
                    $this->formHTML .= $this->getInputHTML($item);
                    break;

                case 'checkbox':
                    $this->formHTML .= $this->getCheckboxHTML($item);
                    break;

                case 'select':
                    $this->formHTML .= $this->getSelectHTML($item);
                    break;

                case 'radio':
                    $this->formHTML .= $this->getRadioHTML($item);
                    break;

                case 'colReset':
                    $this->formHTML .= $this->getColResetHTML();
                    break;

                case 'text':
                    $this->formHTML .= $this->getTextHTML($item);
                    break;

                case 'buttonSubmit':
                    $this->formHTML .= $this->getButtonSubmitHTML($item);
                    break;
                
                default:
                    $this->formHTML .= $this->getInputHTML($item);
                    break;
            }


        }

        return str_replace('{FORM}', $this->formHTML, $this->container);

    }

     /**
     * Render database field
     * 
     * @return array database field
     */
    public function getFormDBFields(){

        $fields = [];

        foreach($this->formData as $key => $item){
            
            switch ($item->type) {

                case 'input':
                    $return = $this->getInputDB($item);
                    break;

                case 'checkbox':
                    $return = $this->getCheckboxDB($item);
                    break;

                case 'select':
                    $return = $this->getSelectDB($item);
                    break;

                case 'radio':
                    $return = $this->getRadioDB($item);
                    break;
                
                default:
                    $return = false;
                    break;
            }

            if($return !== false){
                array_push($fields, $return);
            }


        }

        return $fields;

    }

    /**
     * COMPONENTS
     */


    /**
     * Component HTML - Input
     * 
     * @param object $item - Input item
     */
    protected function getInputHTML($item){

        $id = "input-".mt_rand(1000,9999).'-'.$item->db_field;

        // Defaults
        if(!isset($item->size)) $item->size = 12;

        $return = '
        <div class="'.$this->styles['form-group'].' '.$this->styles['col'].'-'.$item->size.'">
            <label 
                for="'.$id.'"
                title="'.($item->description->type === 'title' ? $item->description->value->{$this->language} : null) .'">'.$item->title->{$this->language}.'</label>
            <input
                id="'.$id.'"
                name="'.$item->db_field.'"
                type="'.$item->subtype.'"
                placeholder="'.$item->placeholder->{$this->language}.'"
                value="'.$item->value.'"
                class="'.$this->styles['form-control'].'"
                '.($item->required ? 'required' : null) .'>
        </div>
        ';

        return trim(preg_replace('/\s\s+/', ' ', $return));

    }

    /**
     * Component DBField - Input
     * 
     * @param object $item - Input item
     * @return object column data
     */
    protected function getInputDB($item){

        switch($item->subtype){
            case 'text':
            case 'email':
            case 'tel':
            case 'password':
            case 'color':
            case 'search':
            case 'url':
                $type = 'TINYTEXT';
                break;

            case 'date':
            case 'datetime-local':
            case 'week':
            case 'month':
            case 'time':
                $type = 'TIMESTAMP';
                break;

            case 'number':
            case 'range':
                $type = 'INT';
                break;

            default:
                $type = 'TINYTEXT';
                break;
        }

        return [
            'name' => $item->db_field,
            'type' => $type,
            'nullable' => true
        ];

    }

    /**
     * Component HTML - Checkbox
     * 
     * @param object $item - Input item
     * @return string HTML
     */
    protected function getCheckboxHTML($item){

        $id = "input-".mt_rand(1000,9999).'-'.$item->db_field;

        // Defaults
        if(!isset($item->size)) $item->size = 12;

        $return = '
        <div class="'.$this->styles['col'].'-'.$item->size.'">
            <div class="'.$this->styles['form-check'].'">
                <input 
                    type="checkbox" 
                    name="'.$item->db_field.'" 
                    class="'.$this->styles['form-check-input'].'" 
                    id="'.$id.'" 
                    '.($item->value ? 'checked' : null) .'
                    '.($item->required ? 'required' : null) .'>
                <label 
                    class="'.$this->styles['form-check-label'].'"
                    for="'.$id.'"
                    title="'.($item->description->type === 'title' ? $item->description->value->{$this->language} : null) .'">'.$item->title->{$this->language}.'</label>
            </div>
        </div>
        ';

        return trim(preg_replace('/\s\s+/', ' ', $return));

    }

    /**
     * Component DBField - Checkbox
     * 
     * @param object $item - Input item
     * @return object column data
     */
    protected function getCheckboxDB($item){

        return [
            'name' => $item->db_field,
            'type' => 'TINYINT(1)',
            'default' => 0
        ];

    }

    /**
     * Component HTML - Radio
     * A group of radio buttons
     * 
     * @param object $item - Input item
     * @return string HTML
     */
    protected function getRadioHTML($item){

        $id = "input-".mt_rand(1000,9999).'-'.$item->db_field;

        // Defaults
        if(!isset($item->size)) $item->size = 12;

        $options = '';
        $index = 1;
        foreach($item->options as $value => $option){
            
            if(is_string($option)){
                $optionsString = $option;
            }elseif(is_object($option) && isset($option->{$this->language})){
                $optionsString = $option->{$this->language};
            }else{
                $optionsString = $value;
            }

            $options .='
            <div class="'.$this->styles['form-check'].'">
                <input 
                    type="radio" 
                    name="'.$item->db_field.'" 
                    class="'.$this->styles['form-check-input'].'" 
                    id="'.$id.'-'.$index.'" 
                    '.($item->value ? 'checked' : null) .'
                    '.($item->required ? 'required' : null) .'>
                <label 
                    class="'.$this->styles['form-check-label'].'"
                    for="'.$id.'-'.$index.'">'.$optionsString.'</label>
            </div>
            ';

            $index++;
        }

        $return = '
        <div class="'.$this->styles['col'].'-'.$item->size.' mt-3">
        <label 
            for="'.$id.'"
            title="'.($item->description->type === 'title' ? $item->description->value->{$this->language} : null) .'">'.$item->title->{$this->language}.'</label>'.$options.'</div>
        ';

        return trim(preg_replace('/\s\s+/', ' ', $return));

    }

    /**
     * Component DBField - Radio
     * 
     * @param object $item - Input item
     * @return object column data
     */
    protected function getRadioDB($item){

        return [
            'name' => $item->db_field,
            'type' => 'TINYTEXT',
            'nullable' => true
        ];

    }

    /**
     * Component HTML - Select
     * 
     * @param object $item - Input item
     * @return string HTML
     */
    protected function getSelectHTML($item){

        $id = "text-".mt_rand(1000,9999).'-'.$item->db_field;

        // Defaults
        if(!isset($item->size)) $item->size = 12;

        $options = '';
        foreach($item->options as $value => $option){
            
            if(is_string($option)){
                $optionsString = $option;
            }elseif(is_object($option) && isset($option->{$this->language})){
                $optionsString = $option->{$this->language};
            }else{
                $optionsString = $value;
            }

            $options .= '<option value="'.$value.'" '.( $item->value == $value ? 'selected' : null).'>'.$optionsString.'</option>';

        }

        $return = '
        <div class="'.$this->styles['form-group'].' '.$this->styles['col'].'-'.$item->size.'">
            <label 
                for="'.$id.'"
                title="'.($item->description->type === 'title' ? $item->description->value->{$this->language} : null) .'">'.$item->title->{$this->language}.'</label>
            <select
                id="'.$id.'"
                name="'.$item->db_field.'"
                '.(isset($item->placeholder->{$this->language}) ? 'placeholder="'.$item->placeholder->{$this->language}.'"' : null).'
                class="'.$this->styles['form-control'].'"
                '.($item->required ? 'required' : null) .'>'.$options.'</select>
        </div>
        ';

        return trim(preg_replace('/\s\s+/', ' ', $return));

    }

    /**
     * Component DBField - Select
     * 
     * @param object $item - Input item
     * @return object column data
     */
    protected function getSelectDB($item){

        return [
            'name' => $item->db_field,
            'type' => 'TINYTEXT',
            'nullable' => true
        ];

    }

    /**
     * Component HTML - Reset collum
     * 
     * @return string HTML
     */
    protected function getColResetHTML(){

        return '<div class="col-12"></div>';

    }

    /**
     * Component HTML - Text
     * 
     * @param object $item - Input item
     * @return string HTML
     */
    protected function getTextHTML($item){

        $id = "text-".mt_rand(1000,9999);
        $allowed = '<p><span><a><b><i><ul><ol><li><h1><h2><h3><h4><h5><h6><strong><italic><code><pre><mark><em><small><del><ins><sub><sup><q><blockquote><abbr><address><cite><bdo><br>';

        // Defaults
        if(!isset($item->size)) $item->size = 12;

        $return = '<div id="'.$id.'" class="'.$this->styles['col'].'-'.$item->size.'">'.strip_tags($item->value, $allowed).'</div>';

        return trim(preg_replace('/\s\s+/', ' ', $return));

    }

    /**
     * Component HTML - Submit button
     * 
     * @param object $item - Input item
     * @return string HTML
     */
    protected function getButtonSubmitHTML($item){

        return '<div class="col-12"><button type="submit" class="'.$this->styles['button-classes'].'">'.$item->title->{$this->language}.'</button></div>';

    }

}

?>