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
                    $this->formHTML .= $this->getCheckboxType($item); // TODO
                    break;

                case 'select':
                    $this->formHTML .= $this->getSelectType($item); // TODO
                    break;

                case 'radio':
                    $this->formHTML .= $this->getRadioType($item); // TODO
                    break;

                case 'colReset':
                    $this->formHTML .= $this->getColResetType();
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
     * Component Input
     * 
     * @param object $item - Input item
     */
    protected function getInputType($item){

        $id = "input-".mt_rand(1000,9999).'-'.$item->db_field;

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

    protected function getCheckboxType($item){

        $id = "input-".mt_rand(1000,9999).'-'.$item->db_field;

        $return = '
        <div class="'.$this->styles['form-check'].' '.$this->styles['col'].'-'.$item->size.'">
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
        ';

        return trim(preg_replace('/\s\s+/', ' ', $return));

    }

    /**
     * Component reset collum
     */
    protected function getColResetType(){

        return '<div class="col-12"></div>';

    }

    /**
     * Component submit button
     */
    protected function getButtonSubmit($item){

        return '<div class="col-12"><button type="submit" class="'.$this->styles['button-classes'].'">'.$item->title->{$this->language}.'</button></div>';

    }

}

?>