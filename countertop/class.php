<?php
/******************************************************************************
 Pepper
 
 Developer		: Brandon Kelly
 Plug-in Name	: Countertop
 
 http://brandon-kelly.com/

 ******************************************************************************/
 
$installPepper = "BK_Countertop";
	
class BK_Countertop extends Pepper
{
	var $version	= 100;
	var $info		= array
	(
		'pepperName'	=> 'Countertop',
		'pepperUrl'		=> 'http://brandon-kelly.com/countertop/',
		'pepperDesc'	=> 'Grab the number of rows in your database table, right from Mint.',
		'developerName'	=> 'Brandon Kelly',
		'developerUrl'	=> 'http://brandon-kelly.com/'
	);
	var $panes = array
	(
		'Counter' => array
		(
			'Total'
		)
	);
	var $prefs = array
	(
		'table' => ''
	);

	/**************************************************************************
	 isCompatible()
	 **************************************************************************/
	function isCompatible()
	{
		if ($this->Mint->version >= 120)
		{
			return array
			(
				'isCompatible'	=> true
			);
		}
		else
		{
			return array
			(
				'isCompatible'	=> false,
				'explanation'	=> '<p>This Pepper is only compatible with Mint 1.2 and higher.</p>'
		);
		}
	}
	
	/**************************************************************************
	 onDisplay()
	 **************************************************************************/
	function onDisplay($pane, $tab, $column = '', $sort = '')
	{
		$html = '';
		switch($pane) 
		{
			case 'Counter':
				switch($tab)
				{
					case 'Total':
						$html .= $this->getHTML_CounterTotal();
						break;
				}
			break;
		}
		return $html;
	}
	
	/**************************************************************************
	 onDisplayPreferences()
	 **************************************************************************/
	function onDisplayPreferences() 
	{
		$preferences['Database'] = <<<HERE
<table>
	<tr>
		<td>
			<p>If the table you&rsquo;d like to count is in a different database than the one containing your Mint records, you must specify it in:</p>
			<code>pepper/brandonkelly/countertop/db.php</code>
		</td>
	</tr>
</table>

HERE;

		$preferences['Counter'] = <<<HERE
<table>
	<tr>
		<th scope="row">Table Name</th>
		<td><span><input type="text" name="table" value="{$this->prefs['table']}" /></span></td>
	</tr>
</table>

HERE;

		return $preferences;
	}
	
	/**************************************************************************
	 onSavePreferences()
	 **************************************************************************/
	function onSavePreferences() 
	{
		$this->prefs['table'] = $this->escapeSQL($_POST['table']);
	}
	
	
	
	function getHTML_CounterTotal()
	{
		if (!$this->prefs['table'])
		{
			return $this->getHTML_Error("Specify your table name in the <a href='?preferences'>Preferences</a>.");
		}
		
		$query = "SELECT COUNT(*) AS `total` FROM `{$this->prefs['table']}`";
		
		include "pepper/brandonkelly/countertop/db.php";
		if ($countertop_db['database'])
		{
			$countertopConn = mysql_connect
			(
				$countertop_db['server']   ? $countertop_db['server']   : $this->db['server'],
				$countertop_db['username'] ? $countertop_db['username'] : $this->db['username'],
				$countertop_db['password'] ? $countertop_db['password'] : $this->db['password'],
				true
			);
			
			if (!$countertopConn)
			{
				return $this->getHTML_Error("Could not connect to the server.", true);
			}
			
			if (!mysql_select_db($countertop_db['database'], $countertopConn))
			{
				mysql_close($countertopConn);
				return $this->getHTML_Error("Could not connect to the database.", true);
			}
			
			$result = mysql_query($query, $countertopConn);
			
			mysql_close($countertopConn);
		}
		else
		{
			$result = $this->query($query);
		}
		
		if (!$result)
		{
			return $this->getHTML_Error("The table &ldquo;{$this->prefs['table']}&rdquo; does not exist.");
		}
		
		$rows = mysql_result($result, 0);
		return "<div style='text-align: center; padding: 2px 0;'><span style='font-size: 4em; line-height: 1em;'>{$rows}</span><br />rows in {$this->prefs['table']}</div>";
	}
	
	function getHTML_Error($msg, $checkSettings = false)
	{
		$html = "<div style='line-height: 2em; text-align: center;'><strong>{$msg}</strong>";
		if ($checkSettings)
		{
			$html .= "<br />Please check your settings in:<br /><code>pepper/brandonkelly/countertop/db.php</code>";
		}
		$html .= "</div>";
		
		return $html;
	}
}
?>