<?
DSApi::reg_eventType('onerror','TFormProtect::callError',array('self'),'TFormProtect');

class TFormProtect extends __TNoVisual {
    
    public $class_name_ex = __CLASS__;
    #public $icon = 'T';    
	
	function callError($self){
		DSApi::callEvent($self, array(), 'OnError');
	}
    
	function __trialTime($self){
		
		$self = c($self);
		$self->showAuth();
	}
	
	function __onStart($self){
		
		$self = c($self);
		$data = $GLOBALS['_FORMS'][ $self->_form ];
		DSApi::initFormEx($data, $self->_form);
		
		$self->set_form( $self->_form );
		if ( $self->trialTime ){
			setTimeout( $self->trialTime * 1000, 'TFormProtect::__trialTime('.$self->self.'); _empty');
		}
		
		if ($self->active)
			$self->showAuth();
	}
	
	function showAuth(){
		global $_FORMS;
		global $mainForm;
		$form = _c($this->_form);
		
		$onShow = $this->onShow;
		if ( $onShow ){
			eval($onShow.'('.$this->self.');');
		}
		
		$edit = c($this->inputKey);
		
		if ( !$form && !$edit ){
		
			if ( !$form )
				alert('[TFormProtect "'.$this->name.'"] Not found auth form');
				
			if ( !$edit )
				alert('[TFormProtect "'.$this->name.'"] Not found input key component');
				
			return;
		}
		
		if ( $form->showModal() != mrOk ){
			
			$mainForm = false;
			app::close();
			
		} else {
			
			
			if ( $edit->text != $this->key ){
				
				$form = $GLOBALS['_FORMS'][ $this->errForm ];
				if ( $form ){
					DSApi::initFormEx($form, $this->errForm);
					$form->showModal();
				}
				
				$onError = $this->onError;
				if ( $onError ){
					eval($onError.'('.$this->self.');');
				}
		
				$this->showAuth();
			}
		}
	}
    
    function __initComponentInfo($init = true){
	
		parent::__initComponentInfo($init);
		DSApi::reg_startFunc('TFormProtect::__onStart('.$this->self.')');
    }
    
    public function __construct($onwer=nil,$init=true,$self=nil){
		
		parent::__construct($onwer, $init, $self);
  
        if ($init){
			$this->key = '123456';
			$this->trialTime = 5;
		}
    }
	
	public function set_form($v){
		
		if ( !$v ){
			$this->_form = '';
			return ;
		}
		
		if ( is_string( $v ) && !$GLOBALS['APP_DESIGN_MODE'] )
		    $form = c($v);
		else
			$form = $v;
		
		if ( !$GLOBALS['APP_DESIGN_MODE'] && !($form instanceof TForm) ){
			pre('������� ����� ��� �����, � �� ���-�� ������!');
		}
		
		$this->_form = $form->self;
	}
}
?>