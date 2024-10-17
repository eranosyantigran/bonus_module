<?
IncludeModuleLangFile(__FILE__);
class CModuleOptionsITBBonus
{
	public $arCurOptionValues = array();
	
	private $module_id = '';
	private $arTabs = array();
	private $arGroups = array();
	private $arOptions = array();
	private $need_access_tab = false;
	
	public function CModuleOptionsITBBonus($module_id, $arTabs, $arGroups, $arOptions, $need_access_tab = false)
	{
		$this->module_id = $module_id;
		$this->arTabs = $arTabs;
		$this->arGroups = $arGroups;
		$this->arOptions = $arOptions;
		$this->need_access_tab = $need_access_tab;
		
		if($need_access_tab)
			$this->arTabs[] = array(
				'DIV' => 'edit_access_tab',
				'TAB' => GetMessage("ITB_ACCESS_TAB"),
				'ICON' => '',
				'TITLE' => GetMessage("ITB_ACCESS_TITLE")
			);
		
		if($_REQUEST['update'] == 'Y' && check_bitrix_sessid())
			$this->SaveOptions();
		
		$this->GetCurOptionValues();
	}
	
	private function SaveOptions()
	{
		foreach($this->arOptions as $opt => $arOptParams)
		{
			if($arOptParams['TYPE'] != 'CUSTOM')
			{
				$val = $_REQUEST[$opt];
	
				if($arOptParams['TYPE'] == 'CHECKBOX' && $val != 'Y')
					$val = 'N';
				elseif(is_array($val))
					$val = serialize($val);

				COption::SetOptionString($this->module_id, $opt, $val);
			}
		}
		LocalRedirect('/bitrix/admin/settings.php?lang=ru&mid=bonus_itb&mid_menu=1');
	}
	
	private function GetCurOptionValues()
	{
		foreach($this->arOptions as $opt => $arOptParams)
		{
			if($arOptParams['TYPE'] != 'CUSTOM')
			{
				$this->arCurOptionValues[$opt] = COption::GetOptionString($this->module_id, $opt, $arOptParams['DEFAULT']);
				if(in_array($arOptParams['TYPE'], array('MSELECT')))
					$this->arCurOptionValues[$opt] = unserialize($this->arCurOptionValues[$opt]);
			}
		}
	}
	
	public function ShowHTML()
	{
//		$path = $_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/bonus_itb/classes/module-options/version_access.txt';
//		if(file_exists($path))
//			$access_v_3 = file_get_contents($path);
		
		global $APPLICATION;

		$arP = array();
		
		foreach($this->arGroups as $group_id => $group_params)
			$arP[$group_params['TAB']][$group_id] = array();
		CJSCore::Init(array('jquery2'));
		if(is_array($this->arOptions))
		{
			foreach($this->arOptions as $option => $arOptParams)
			{
				$val = $this->arCurOptionValues[$option];

				if($arOptParams['SORT'] < 0 || !isset($arOptParams['SORT']))
					$arOptParams['SORT'] = 0;
				
				$label = (isset($arOptParams['TITLE']) && $arOptParams['TITLE'] != '') ? $arOptParams['TITLE'] : '';
				$opt = htmlspecialcharsbx($option);

				switch($arOptParams['TYPE'])
				{
					case 'AJAX':
						$input = '<span name="'.$opt.'" id="'.$opt.'" onclick="FreeBonus(); return false;" >'.$arOptParams['VALUE'].'</span>';
						$input .= '<div id="ajax_result"></div>';
				?>
					<style type="text/css">
                    #FREE_BONUS_BUTTON {
                        padding:5px;
                        -webkit-border-radius: 4px;
                        border-radius: 4px;
                        border: none;
                        /* border-top: 1px solid #fff; */
                        -webkit-box-shadow: 0 0 1px rgba(0,0,0,.11), 0 1px 1px rgba(0,0,0,.3), inset 0 1px #fff, inset 0 0 1px rgba(255,255,255,.5);
                        box-shadow: 0 0 1px rgba(0,0,0,.3), 0 1px 1px rgba(0,0,0,.3), inset 0 1px 0 #fff, inset 0 0 1px rgba(255,255,255,.5);
                        background-color: #e0e9ec;
                        background-image: -webkit-linear-gradient(bottom, #d7e3e7, #fff)!important;
                        background-image: -moz-linear-gradient(bottom, #d7e3e7, #fff)!important;
                        background-image: -ms-linear-gradient(bottom, #d7e3e7, #fff)!important;
                        background-image: -o-linear-gradient(bottom, #d7e3e7, #fff)!important;
                        background-image: linear-gradient(bottom, #d7e3e7, #fff)!important;
                        color: #3f4b54;
                        cursor: pointer;
                        display: inline-block;
                        font-family: "Helvetica Neue",Helvetica,Arial,sans-serif;
                        font-weight: bold;
                        font-size: 13px;
                        text-shadow: 0 1px rgba(255,255,255,0.7);
                        text-decoration: none;
                        position: relative;
                        vertical-align: middle;
                        -webkit-font-smoothing: antialiased;
                    }
					a.lgb_instruction {
						font-size:12px;
						color:#D24;
					}
                    </style>
                    
                    <script type="text/javascript">
                        function FreeBonus(event)
                        {
                            var select1 = document.getElementById('FREE_BONUS_GROUPS[]');
                            var selected1 = [];
                            for (var i = 0; i < select1.length; i++) {
                                if (select1.options[i].selected) selected1.push(select1.options[i].value);
                            }
                            //console.log(selected1);	
                            var balls = document.getElementById('FREE_BONUS_BONUS').value;
                            //console.log(balls);	
                            
							BX.ajax({
								   url: '/bitrix/components/logictim/bonus.freeadd/addFreeBonus.php',
								   data: {'select1':selected1, 'balls':balls},
								   method: 'POST',
								   dataType: 'html',
								   timeout: 120,
								   async: true,
								   processData: true,
								   scriptsRunFirst: true,
								   emulateOnload: true,
								   start: true,
								   cache: false,
								   onsuccess: function(data){
									   document.getElementById('ajax_result').innerHTML = data;  
									console.log(data);
								   },
								   onfailure: function(){}
							}); 
                        }
						
						//Show or hide elements from delect
						BX.ready(function(){
							$("select.LGB_PARENT_SELECT").change(function() {
								var select_name = $(this).attr('name');
								var select_val = $(this).val();
								$("tr."+select_name).each(function(i,elem){
									if($(this).hasClass(select_name+'_'+select_val))
										$(this).show();
									else
										$(this).hide();
								});
								
							});
							
							$("select.LGB_PARENT_SELECT").each(function(i,elem) {
								var select_name = $(this).attr('name');
								var select_val = $(this).val();
								$("tr."+select_name).each(function(i,elem){
									if($(this).hasClass(select_name+'_'+select_val))
										$(this).show();
									else
										$(this).hide();
								});
								
								
							});
						});

                    </script>
                    <?
					break;
					case 'CHECKBOX':
						$input = '<input type="checkbox" name="'.$opt.'" id="'.$opt.'" value="Y"'.($val == 'Y' ? ' checked' : '').' '.($arOptParams['REFRESH'] == 'Y' ? 'onclick="document.forms[\''.$this->module_id.'\'].submit();"' : '').' />';
						break;
					case 'TEXT':
						if(!isset($arOptParams['COLS']))
							$arOptParams['COLS'] = 25;
						if(!isset($arOptParams['ROWS']))
							$arOptParams['ROWS'] = 5;
						$input = '<textarea  cols="'.$arOptParams['COLS'].'" rows="'.$arOptParams['ROWS'].'" name="'.$opt.'">'.htmlspecialcharsbx($val).'</textarea>';
						if($arOptParams['REFRESH'] == 'Y')
							$input .= '<input type="submit" name="refresh" value="OK" />';
						break;
					case 'SELECT':
						
						if($option == 'MODULE_VERSION' && $access_v_3 != 'Y')
							$arOptParams["CLASS"] = 'hidden';
						if($arOptParams["CLASS"] != '')
							$classSel = 'class="'.$arOptParams["CLASS"].'"';
						else
							$classSel = '';
						$input = SelectBoxFromArray($opt, $arOptParams['VALUES'], $val, '', $classSel, ($arOptParams['REFRESH'] == 'Y' ? true : false), ($arOptParams['REFRESH'] == 'Y' ? $this->module_id : ''));
						if($arOptParams['REFRESH'] == 'Y')
							$input .= '<input type="submit" name="refresh" value="OK" />';
						break;
					case 'MSELECT':
						$input = SelectBoxMFromArray($opt.'[]', $arOptParams['VALUES'], $val);
						if($arOptParams['REFRESH'] == 'Y')
							$input .= '<input type="submit" name="refresh" value="OK" />';
						break;
					case 'COLORPICKER':
						if(!isset($arOptParams['FIELD_SIZE']))
							$arOptParams['FIELD_SIZE'] = 25;
						ob_start();
						echo 	'<input id="__CP_PARAM_'.$opt.'" name="'.$opt.'" size="'.$arOptParams['FIELD_SIZE'].'" value="'.htmlspecialcharsbx($val).'" type="text" style="float: left;" '.($arOptParams['FIELD_READONLY'] == 'Y' ? 'readonly' : '').' />
								<script>
									function onSelect_'.$opt.'(color, objColorPicker)
									{
										var oInput = BX("__CP_PARAM_'.$opt.'");
										oInput.value = color;
									}
								</script>';
						$APPLICATION->IncludeComponent('bitrix:main.colorpicker', '', Array(
								'SHOW_BUTTON' => 'Y',
								'ID' => $opt,
								'NAME' => GetMessage("ITB_COLOR_SELECT"),
								'ONSELECT' => 'onSelect_'.$opt
							), false
						);
						$input = ob_get_clean();
						if($arOptParams['REFRESH'] == 'Y')
							$input .= '<input type="submit" name="refresh" value="OK" />';
						break;
					case 'CALENDAR':
						if(!isset($arOptParams['FIELD_SIZE']))
							$arOptParams['FIELD_SIZE'] = 25;
						ob_start();
						echo 	'<input id="__CP_PARAM_'.$opt.'" name="'.$opt.'" size="'.$arOptParams['FIELD_SIZE'].'" value="'.htmlspecialcharsbx($val).'" type="text" style="float: left;" '.($arOptParams['FIELD_READONLY'] == 'Y' ? 'readonly' : '').' />';
						$APPLICATION->IncludeComponent('bitrix:main.calendar', '', Array(
								"SHOW_INPUT" => "N",
								 "FORM_NAME" => "",
								 "INPUT_NAME" => "$opt",
								 "INPUT_VALUE" => "",
								 "SHOW_TIME" => "Y",
								 "HIDE_TIMEBAR" => "Y",
							), false
						);
						$input = ob_get_clean();
						if($arOptParams['REFRESH'] == 'Y')
							$input .= '<input type="submit" name="refresh" value="OK" />';
						break;
					case 'FILE':
						if(!isset($arOptParams['FIELD_SIZE']))
							$arOptParams['FIELD_SIZE'] = 25;
						if(!isset($arOptParams['BUTTON_TEXT']))
							$arOptParams['BUTTON_TEXT'] = '...';
						CAdminFileDialog::ShowScript(Array(
							'event' => 'BX_FD_'.$opt,
							'arResultDest' => Array('FUNCTION_NAME' => 'BX_FD_ONRESULT_'.$opt),
							'arPath' => Array(),
							'select' => 'F',
							'operation' => 'O',
							'showUploadTab' => true,
							'showAddToMenuTab' => false,
							'fileFilter' => '',
							'allowAllFiles' => true,
							'SaveConfig' => true
						));
						$input = 	'<input id="__FD_PARAM_'.$opt.'" name="'.$opt.'" size="'.$arOptParams['FIELD_SIZE'].'" value="'.htmlspecialcharsbx($val).'" type="text" style="float: left;" '.($arOptParams['FIELD_READONLY'] == 'Y' ? 'readonly' : '').' />
									<input value="'.$arOptParams['BUTTON_TEXT'].'" type="button" onclick="window.BX_FD_'.$opt.'();" />
									<script>
										setTimeout(function(){
											if (BX("bx_fd_input_'.strtolower($opt).'"))
												BX("bx_fd_input_'.strtolower($opt).'").onclick = window.BX_FD_'.$opt.';
										}, 200);
										window.BX_FD_ONRESULT_'.$opt.' = function(filename, filepath)
										{
											var oInput = BX("__FD_PARAM_'.$opt.'");
											if (typeof filename == "object")
												oInput.value = filename.src;
											else
												oInput.value = (filepath + "/" + filename).replace(/\/\//ig, \'/\');
										}
									</script>';
						if($arOptParams['REFRESH'] == 'Y')
							$input .= '<input type="submit" name="refresh" value="OK" />';
						break;
					case 'CUSTOM':
						$input = $arOptParams['VALUE'];
						break;
					case 'INT':
						if(!isset($arOptParams['SIZE']))
							$arOptParams['SIZE'] = 25;
						if(!isset($arOptParams['MAXLENGTH']))
							$arOptParams['MAXLENGTH'] = 255;
						if(isset($arOptParams['PLACE_HOLDER']))
							$placeholder = 'placeholder="'.$arOptParams['PLACE_HOLDER'].'"';
						else
							$placeholder = '';
						
						if($arOptParams['STEP'] != '')
							$step = 'step="'.$arOptParams['STEP'].'"';
						else
							$step = '';
						if($arOptParams['MIN'] > 0)
							$min = 'min="'.$arOptParams['MIN'].'"';
						else
							$min = 'min="0"';
						if($arOptParams['MAX'] > 0)
							$max = 'max="'.$arOptParams['MAX'].'"';
						else
							$max = '';
							
						$input = '<input id="'.$opt.'" type="number" size="'.$arOptParams['SIZE'].'" maxlength="'.$arOptParams['MAXLENGTH'].'" value="'.htmlspecialcharsbx($val).'" name="'.htmlspecialcharsbx($option).'"'.$placeholder.' '.$step.' '.$min.' '.$max.'" />';
						if($arOptParams['REFRESH'] == 'Y')
							$input .= '<input type="submit" name="refresh" value="OK" />';
						break;
					default:
						if(!isset($arOptParams['SIZE']))
							$arOptParams['SIZE'] = 25;
						if(!isset($arOptParams['MAXLENGTH']))
							$arOptParams['MAXLENGTH'] = 255;
						if(isset($arOptParams['PLACE_HOLDER']))
							$placeholder = 'placeholder="'.$arOptParams['PLACE_HOLDER'].'"';
						else
							$placeholder = '';
						
						$input = '<input id="'.$opt.'" type="text" size="'.$arOptParams['SIZE'].'" maxlength="'.$arOptParams['MAXLENGTH'].'" value="'.htmlspecialcharsbx($val).'" name="'.htmlspecialcharsbx($option).'"'.$placeholder.'" />';
						if($arOptParams['REFRESH'] == 'Y')
							$input .= '<input type="submit" name="refresh" value="OK" />';
						break;
				}

				if(isset($arOptParams['NOTES']) && $arOptParams['NOTES'] != '')
					$input .= 	'<div class="notes">
									<table cellspacing="0" cellpadding="0" border="0" class="notes">
										<tbody>
											<tr class="top">
												<td class="left"><div class="empty"></div></td>
												<td><div class="empty"></div></td>
												<td class="right"><div class="empty"></div></td>
											</tr>
											<tr>
												<td class="left"><div class="empty"></div></td>
												<td class="content">
													'.$arOptParams['NOTES'].'
												</td>
												<td class="right"><div class="empty"></div></td>
											</tr>
											<tr class="bottom">
												<td class="left"><div class="empty"></div></td>
												<td><div class="empty"></div></td>
												<td class="right"><div class="empty"></div></td>
											</tr>
										</tbody>
									</table>
								</div>';

				
				if($arOptParams["CLASS"] != '')
					$classTr = 'class="'.$arOptParams["CLASS"].'"';
				else
					$classTr = '';
						
				if(isset($arOptParams["ROW"]))
				{
					
					if($arOptParams["ROW"] == 'BEGIN')
					{
						$row = '<tr '.$classTr.'><td valign="top" width="" colspan="2" style="text-align: center; padding-top: 10px;">'.$arOptParams["HEADER"].'<table style="text-align: center; margin: auto;"><tr><td>'.$label.'</td>'.'<td>'.$input.'</td>';
					}
					elseif($arOptParams["ROW"] == 'END')
					{
						$row .= '<td>'.$label.'</td><td>'.$input.'</td><td>'.$arOptParams["TITLE_2"].'</td>'.'</tr></table></td></tr>';
						$arP[$this->arGroups[$arOptParams['GROUP']]['TAB']][$arOptParams['GROUP']]['OPTIONS'][] = $row;
					}
					else
					{
						$row .= 'TTT';
					}
					
				}
				else
				{
					$arP[$this->arGroups[$arOptParams['GROUP']]['TAB']][$arOptParams['GROUP']]['OPTIONS'][] = $label != '' ? '<tr '.$classTr.'><td valign="top" width="40%">'.$label.'</td><td valign="top" nowrap>'.$input.'</td></tr>' : '<tr><td valign="top" colspan="2" align="center">'.$input.'</td></tr>';
				}
				
				$arP[$this->arGroups[$arOptParams['GROUP']]['TAB']][$arOptParams['GROUP']]['OPTIONS_SORT'][] = $arOptParams['SORT'];
			}

			$tabControl = new CAdminTabControl('tabControl', $this->arTabs);
			$tabControl->Begin();
			echo '<form name="'.$this->module_id.'" method="POST" action="'.$APPLICATION->GetCurPage().'?mid='.$this->module_id.'&lang='.LANGUAGE_ID.'" enctype="multipart/form-data">'.bitrix_sessid_post();

			foreach($arP as $tab => $groups)
			{
				$tabControl->BeginNextTab();

				foreach($groups as $group_id => $group)
				{
					if(sizeof($group['OPTIONS_SORT']) > 0)
					{
						
						if($this->arGroups[$group_id]["CLASS"] != '')
							$classTr = $this->arGroups[$group_id]["CLASS"];
						else
							$classTr = '';
						
						echo '<tr class="heading '.$classTr.'"><td colspan="2">'.$this->arGroups[$group_id]['TITLE'].'</td></tr>';
						
						array_multisort($group['OPTIONS_SORT'], $group['OPTIONS']);
						foreach($group['OPTIONS'] as $opt)
							echo $opt;
					}
				}
			}
			
			if($this->need_access_tab)
			{
				$tabControl->BeginNextTab();
				require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/admin/group_rights.php");
			}
			
			
			$tabControl->Buttons();
			
			echo 	'<input type="hidden" name="update" value="Y" />
					<input type="submit" name="save" value="'.GetMessage("ITB_SAVE").'" />
					<input type="reset" name="reset" value="'.GetMessage("ITB_CANCEL").'" />
					</form>';

			$tabControl->End();
		}
	}
}
?>
<style type="text/css">
.hidden {
	display:none;
}
</style>
<script type="text/javascript">
//Show or hide elements from select
	BX.ready(function(){
		$("select.LGB_PARENT_SELECT").change(function() {
			var select_name = $(this).attr('name');
			var select_val = $(this).val();
			$("tr."+select_name).each(function(i,elem){
				if($(this).hasClass(select_name+'_'+select_val))
					$(this).show();
				else
					$(this).hide();
			});
			
		});
		
		$("select.LGB_PARENT_SELECT").each(function(i,elem) {
			var select_name = $(this).attr('name');
			var select_val = $(this).val();
			$("tr."+select_name).each(function(i,elem){
				if($(this).hasClass(select_name+'_'+select_val))
					$(this).show();
				else
					$(this).hide();
			});
			
			
		});
	});

</script>
