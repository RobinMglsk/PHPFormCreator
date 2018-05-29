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
                    $this->formHTML .= $this->getInputType($item);
                    break;

                case 'checkbox':
                    $this->formHTML .= $this->getCheckboxType($item);
                    break;

                case 'select':
                    $this->formHTML .= $this->getSelectType($item);
                    break;

                case 'radio':
                    $this->formHTML .= $this->getRadioType($item);
                    break;

                case 'colReset':
                    $this->formHTML .= $this->getColResetType();
                    break;

                case 'text':
                    $this->formHTML .= $this->getTextType($item);
                    break;

                case 'buttonSubmit':
                    $this->formHTML .= $this->getButtonSubmit($item);
                    break;
                
                default:
                    $this->formHTML .= $this->getInputType($item);
                    break;
            }


        }

        return str_replace('{FORM}', $this->formHTML, $this->container);

    }

    /**
     * COMPONENTS
     */

    /**
     * Component - Input
     * 
     * @param object $item - Input item
     */
    protected function getInputType($item){

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
     * Component - Checkbox
     */
    protected function getCheckboxType($item){

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
     * Component - Radio
     * 
     * A group of radio buttons
     */
    protected function getRadioType($item){

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
     * Component - Select
     */
    protected function getSelectType($item){

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
     * Component - Reset collum
     */
    protected function getColResetType(){

        return '<div class="col-12"></div>';

    }

    /**
     * Component - Text
     */
    protected function getTextType($item){

        $id = "text-".mt_rand(1000,9999);
        $allowed = '<p><span><a><b><i><ul><ol><li><h1><h2><h3><h4><h5><h6><strong><italic><code><pre><mark><em><small><del><ins><sub><sup><q><blockquote><abbr><address><cite><bdo><br>';

        // Defaults
        if(!isset($item->size)) $item->size = 12;

        $return = '<div id="'.$id.'" class="'.$this->styles['col'].'-'.$item->size.'">'.strip_tags($item->value, $allowed).'</div>';

        return trim(preg_replace('/\s\s+/', ' ', $return));

    }

    /**
     * Component - Submit button
     */
    protected function getButtonSubmit($item){

        return '<div class="col-12"><button type="submit" class="'.$this->styles['button-classes'].'">'.$item->title->{$this->language}.'</button></div>';

    }

}

?>