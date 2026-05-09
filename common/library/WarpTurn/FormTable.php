<?php

/**
* Class created by Scott Hovestadt at www.warpturn.com
*
* Redistribution and use in source and binary forms, with or without modification, are permitted provided that the following conditions are met:
* - Redistributions of source code must retain the above copyright notice, this list of conditions and the following disclaimer.
* - Redistributions in binary form must reproduce the above copyright notice, this list of conditions and the following disclaimer in the documentation and/or other materials provided with the distribution.
*
* THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT HOLDER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
*/

class WarpTurn_FormTable extends Zend_Form
{
    /**
     * Decorators to use by default for most form elements.
     *
     * @var array
     */
    protected $_defaultElementDecorators = array(
        array('Label', array('escape' => false)),
        array(array('cellLabel' => 'HtmlTag'), array('tag' => 'td', 'class' => 'cell-label')),
        array(array('cellElementOpenTd' => 'HtmlTag'), array('tag' => 'td', 'class' => 'cell-element', 'openOnly' => true, 'placement' => 'append')),
        array(array('cellElementOpenDiv' => 'HtmlTag'), array('tag' => 'div', 'class' => 'cell-element', 'openOnly' => true, 'placement' => 'append')),
        array('Errors', array('escape' => false)),
        'ViewHelper',
        array('Description', array('tag' => 'div', 'escape' => false, 'class' => 'description')),
        array(array('cellElementCloseDiv' => 'HtmlTag'), array('tag' => 'div', 'closeOnly' => true, 'placement' => 'append')),
        array(array('cellElementCloseTd' => 'HtmlTag'), array('tag' => 'td', 'closeOnly' => true, 'placement' => 'append')),
        array(array('row' => 'HtmlTag'), array('tag' => 'tr')),
    );

    /**
     * Decorator to use for button form elements:
     * input button, input submit, input image
     *
     * @var array
     */
    protected $_buttonElementDecorators = array(
        'ViewHelper',
        'Errors',
        array(array('data' => 'HtmlTag'), array('tag' => 'td', 'class' => 'cell-element')),
        array(array('label' => 'HtmlTag'), array('tag' => 'td', 'placement' => 'prepend', 'class' => 'cell-label')),
        array(array('row' => 'HtmlTag'), array('tag' => 'tr')),
    );

    /**
     * Decorator to use for the file decorator
     *
     * @var array
     */
    protected $_fileElementDecorators = array(
        array('Label', array('escape' => false)),
        array(array('cellLabel' => 'HtmlTag'), array('tag' => 'td', 'class' => 'cell-label')),
        array(array('cellElementOpenTd' => 'HtmlTag'), array('tag' => 'td', 'class' => 'cell-element', 'openOnly' => true, 'placement' => 'append')),
        array(array('cellElementOpenDiv' => 'HtmlTag'), array('tag' => 'div', 'class' => 'cell-element', 'openOnly' => true, 'placement' => 'append')),
        array('Errors', array('escape' => false)),
        'File',
        array('Description', array('tag' => 'div', 'escape' => false, 'class' => 'description')),
        array(array('cellElementCloseDiv' => 'HtmlTag'), array('tag' => 'div', 'closeOnly' => true, 'placement' => 'append')),
        array(array('cellElementCloseTd' => 'HtmlTag'), array('tag' => 'td', 'closeOnly' => true, 'placement' => 'append')),
        array(array('row' => 'HtmlTag'), array('tag' => 'tr')),
    );

    /**
     * Decorator to use for hidden elements
     *
     * @var array
     */
    protected $_hiddenElementDecorators = array(
        'ViewHelper',
    );

    /**
     * Load the default decorators
     *
     * @return void
     */
    public function loadDefaultDecorators()
    {
        if ($this->loadDefaultDecoratorsIsDisabled())
        {
            return;
        }
        $decorators = $this->getDecorators();
        if (empty($decorators))
        {
            $this->addDecorator('FormElements')
                 ->addDecorator('HtmlTag', array('tag' => 'table', 'class' => 'table-detail'))
                 ->addDecorator('Form');
        }
    }

    /**
     * Before calling parent::createElement(), sets decorators based on element type if not already set
     *
     * @return mixed
     */
    public function createElement($type, $name, $options = null)
    {

        if(!isset($options['decorators']))
        {
            $options['decorators'] = $this->_getTypeDecorators($type);
        }

        return parent::createElement($type, $name, $options);
    }

    /**
     * Get decorators for element
     *
     * @param string $type
     * @return array
     */
    private function _getTypeDecorators($type)
    {
        if($type == 'button' || $type == 'submit' || $type == 'image')
        {
            return $this->_buttonElementDecorators;
        }
        elseif($type == 'file')
        {
            return $this->_fileElementDecorators;
        }
        elseif($type == 'hidden')
        {
            return $this->_hiddenElementDecorators;
        }
        else
        {
            return $this->_defaultElementDecorators;
        }
    }
} 
