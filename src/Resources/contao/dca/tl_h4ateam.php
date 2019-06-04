<?php
/**
 * Table tl_team
 */
$GLOBALS ['TL_DCA'] ['tl_h4ateam'] = array
(
    // Config
    'config' => array
	(
		'dataContainer'               => 'Table',
		'ptable'                      => 'tl_season',
		//'ctable'                      => array('tl_content'),
		'switchToEdit'                => true,
		'enableVersioning'            => true,
		'markAsCopy'                  => 'title',
		'onload_callback' => array
		(
			array('tl_h4teams', 'checkPermission'),
		),
		'sql' => array
		(
			'keys' => array
			(
				'id' => 'primary',
				'alias' => 'index',
				'pid,start,stop,published' => 'index'
			)
		)
	),

	// List
	'list' => array
	(
		'sorting' => array
		(
			'mode'                    => 4,
			'fields'                  => array('startTime DESC'),
			'headerFields'            => array('title', 'jumpTo', 'tstamp', 'protected', 'allowComments'),
			'panelLayout'             => 'filter;sort,search,limit',
			'child_record_callback'   => array('tl_h4ateams', 'listEvents')
		),
		'global_operations' => array
		(
			'all' => array
			(
				'label'               => &$GLOBALS['TL_LANG']['MSC']['all'],
				'href'                => 'act=select',
				'class'               => 'header_edit_all',
				'attributes'          => 'onclick="Backend.getScrollOffset()" accesskey="e"'
			)
		),
		'operations' => array
		(
			'edit' => array
			(
				'label'               => &$GLOBALS['TL_LANG']['tl_h4teams']['edit'],
				'href'                => 'table=tl_content',
				'icon'                => 'edit.svg'
			),
			'editheader' => array
			(
				'label'               => &$GLOBALS['TL_LANG']['tl_h4teams']['editmeta'],
				'href'                => 'act=edit',
				'icon'                => 'header.svg'
			),
			'copy' => array
			(
				'label'               => &$GLOBALS['TL_LANG']['tl_h4teams']['copy'],
				'href'                => 'act=paste&amp;mode=copy',
				'icon'                => 'copy.svg'
			),
			'cut' => array
			(
				'label'               => &$GLOBALS['TL_LANG']['tl_h4teams']['cut'],
				'href'                => 'act=paste&amp;mode=cut',
				'icon'                => 'cut.svg'
			),
			'delete' => array
			(
				'label'               => &$GLOBALS['TL_LANG']['tl_h4teams']['delete'],
				'href'                => 'act=delete',
				'icon'                => 'delete.svg',
				'attributes'          => 'onclick="if(!confirm(\'' . $GLOBALS['TL_LANG']['MSC']['deleteConfirm'] . '\'))return false;Backend.getScrollOffset()"'
			),
			'toggle' => array
			(
				'label'               => &$GLOBALS['TL_LANG']['tl_h4teams']['toggle'],
				'icon'                => 'visible.svg',
				'attributes'          => 'onclick="Backend.getScrollOffset();return AjaxRequest.toggleVisibility(this,%s)"',
				'button_callback'     => array('tl_h4teams', 'toggleIcon'),
				'showInHeader'        => true
			),
			'show' => array
			(
				'label'               => &$GLOBALS['TL_LANG']['tl_h4teams']['show'],
				'href'                => 'act=show',
				'icon'                => 'show.svg'
			)
		)
	),

    // Palettes
    'palettes' => array
    (
      'default' => '{title_legend}, title; {image_legend}, ;'
    ),
    // Fields
    'fields' => array
    (
      'id' => array
      (
        'sql'                     => "int(10) unsigned NOT NULL auto_increment"
      ),
      'pid' => array
      (
        'foreignKey'              => 'tl_calendar.title',
        'sql'                     => "int(10) unsigned NOT NULL default '0'",
        'relation'                => array('type'=>'belongsTo', 'load'=>'lazy')
      ),
      'tstamp' => array
      (
        'sql'                     => "int(10) unsigned NOT NULL default '0'"
      ),
      'title' => array
      (
        'label'                   => &$GLOBALS['TL_LANG']['tl_h4teams']['title'],
        'exclude'                 => true,
        'search'                  => true,
        'sorting'                 => true,
        'flag'                    => 1,
        'inputType'               => 'text',
        'eval'                    => array('mandatory'=>true, 'maxlength'=>255, 'tl_class'=>'w50'),
        'sql'                     => "varchar(255) NOT NULL default ''"
      ),
      'alias' => array
      (
        'label'                   => &$GLOBALS['TL_LANG']['tl_h4teams']['alias'],
        'exclude'                 => true,
        'search'                  => true,
        'inputType'               => 'text',
        'eval'                    => array('rgxp'=>'alias', 'doNotCopy'=>true, 'unique'=>true, 'maxlength'=>128, 'tl_class'=>'w50 clr'),
        'save_callback' => array
        (
          array('tl_h4ateams', 'generateAlias')
        ),
        'sql'                     => "varchar(128) BINARY NOT NULL default ''"
      ),
    )
);

/**
 * Provide miscellaneous methods that are used by the data configuration array.
 *
 * @property Contao\Calendar $Calendar
 *
 * @author Leo Feyer <https://github.com/leofeyer>
 */
class tl_h4teams extends Contao\Backend
{

	/**
	 * Import the back end user object
	 */
	public function __construct()
	{
		parent::__construct();
		$this->import('Contao\BackendUser', 'User');
	}

	/**
	 * Check permissions to edit table tl_h4teams
	 *
	 * @throws Contao\CoreBundle\Exception\AccessDeniedException
	 */
	public function checkPermission()
	{
		$bundles = Contao\System::getContainer()->getParameter('kernel.bundles');

		// HOOK: comments extension required
		if (!isset($bundles['ContaoCommentsBundle']))
		{
			$key = array_search('allowComments', $GLOBALS['TL_DCA']['tl_h4teams']['list']['sorting']['headerFields']);
			unset($GLOBALS['TL_DCA']['tl_h4teams']['list']['sorting']['headerFields'][$key]);
		}

		if ($this->User->isAdmin)
		{
			return;
		}

		// Set root IDs
		if (empty($this->User->calendars) || !\is_array($this->User->calendars))
		{
			$root = array(0);
		}
		else
		{
			$root = $this->User->calendars;
		}

		$id = \strlen(Contao\Input::get('id')) ? Contao\Input::get('id') : CURRENT_ID;

		// Check current action
		switch (Contao\Input::get('act'))
		{
			case 'paste':
			case 'select':
				if (!\in_array(CURRENT_ID, $root)) // check CURRENT_ID here (see #247)
				{
					throw new Contao\CoreBundle\Exception\AccessDeniedException('Not enough permissions to access calendar ID ' . $id . '.');
				}
				break;

			case 'create':
				if (!\strlen(Contao\Input::get('pid')) || !\in_array(Contao\Input::get('pid'), $root))
				{
					throw new Contao\CoreBundle\Exception\AccessDeniedException('Not enough permissions to create events in calendar ID ' . Contao\Input::get('pid') . '.');
				}
				break;

			case 'cut':
			case 'copy':
				if (!\in_array(Contao\Input::get('pid'), $root))
				{
					throw new Contao\CoreBundle\Exception\AccessDeniedException('Not enough permissions to ' . Contao\Input::get('act') . ' event ID ' . $id . ' to calendar ID ' . Contao\Input::get('pid') . '.');
				}
				// no break;

			case 'edit':
			case 'show':
			case 'delete':
			case 'toggle':
				$objCalendar = $this->Database->prepare("SELECT pid FROM tl_h4teams WHERE id=?")
											  ->limit(1)
											  ->execute($id);

				if ($objCalendar->numRows < 1)
				{
					throw new Contao\CoreBundle\Exception\AccessDeniedException('Invalid event ID ' . $id . '.');
				}

				if (!\in_array($objCalendar->pid, $root))
				{
					throw new Contao\CoreBundle\Exception\AccessDeniedException('Not enough permissions to ' . Contao\Input::get('act') . ' event ID ' . $id . ' of calendar ID ' . $objCalendar->pid . '.');
				}
				break;

			case 'editAll':
			case 'deleteAll':
			case 'overrideAll':
			case 'cutAll':
			case 'copyAll':
				if (!\in_array($id, $root))
				{
					throw new Contao\CoreBundle\Exception\AccessDeniedException('Not enough permissions to access calendar ID ' . $id . '.');
				}

				$objCalendar = $this->Database->prepare("SELECT id FROM tl_h4teams WHERE pid=?")
											  ->execute($id);

				/** @var Symfony\Component\HttpFoundation\Session\SessionInterface $objSession */
				$objSession = Contao\System::getContainer()->get('session');

				$session = $objSession->all();
				$session['CURRENT']['IDS'] = array_intersect((array) $session['CURRENT']['IDS'], $objCalendar->fetchEach('id'));
				$objSession->replace($session);
				break;

			default:
				if (\strlen(Contao\Input::get('act')))
				{
					throw new Contao\CoreBundle\Exception\AccessDeniedException('Invalid command "' . Contao\Input::get('act') . '".');
				}
				elseif (!\in_array($id, $root))
				{
					throw new Contao\CoreBundle\Exception\AccessDeniedException('Not enough permissions to access calendar ID ' . $id . '.');
				}
				break;
		}
	}

	/**
	 * Auto-generate the event alias if it has not been set yet
	 *
	 * @param mixed                $varValue
	 * @param Contao\DataContainer $dc
	 *
	 * @return mixed
	 *
	 * @throws Exception
	 */
	public function generateAlias($varValue, Contao\DataContainer $dc)
	{
		$aliasExists = function (string $alias) use ($dc): bool
		{
			return $this->Database->prepare("SELECT id FROM tl_h4teams WHERE alias=? AND id!=?")->execute($alias, $dc->id)->numRows > 0;
		};

		// Generate the alias if there is none
		if ($varValue == '')
		{
			$varValue = Contao\System::getContainer()->get('contao.slug')->generate($dc->activeRecord->title, Contao\CalendarModel::findByPk($dc->activeRecord->pid)->jumpTo ?? array(), $aliasExists);
		}
		elseif ($aliasExists($varValue))
		{
			throw new Exception(sprintf($GLOBALS['TL_LANG']['ERR']['aliasExists'], $varValue));
		}

		return $varValue;
	}

	/**
	 * Add the type of input field
	 *
	 * @param array $arrRow
	 *
	 * @return string
	 */
	public function listEvents($arrRow)
	{
		$span = Contao\Calendar::calculateSpan($arrRow['startTime'], $arrRow['endTime']);

		if ($span > 0)
		{
			$date = Contao\Date::parse(Contao\Config::get(($arrRow['addTime'] ? 'datimFormat' : 'dateFormat')), $arrRow['startTime']) . $GLOBALS['TL_LANG']['MSC']['cal_timeSeparator'] . Contao\Date::parse(Contao\Config::get(($arrRow['addTime'] ? 'datimFormat' : 'dateFormat')), $arrRow['endTime']);
		}
		elseif ($arrRow['startTime'] == $arrRow['endTime'])
		{
			$date = Contao\Date::parse(Contao\Config::get('dateFormat'), $arrRow['startTime']) . ($arrRow['addTime'] ? ' ' . Contao\Date::parse(Contao\Config::get('timeFormat'), $arrRow['startTime']) : '');
		}
		else
		{
			$date = Contao\Date::parse(Contao\Config::get('dateFormat'), $arrRow['startTime']) . ($arrRow['addTime'] ? ' ' . Contao\Date::parse(Contao\Config::get('timeFormat'), $arrRow['startTime']) . $GLOBALS['TL_LANG']['MSC']['cal_timeSeparator'] . Contao\Date::parse(Contao\Config::get('timeFormat'), $arrRow['endTime']) : '');
		}

		return '<div class="tl_content_left">' . $arrRow['title'] . ' <span style="color:#999;padding-left:3px">[' . $date . ']</span></div>';
	}


	/**
	 * Return the "toggle visibility" button
	 *
	 * @param array  $row
	 * @param string $href
	 * @param string $label
	 * @param string $title
	 * @param string $icon
	 * @param string $attributes
	 *
	 * @return string
	 */
	public function toggleIcon($row, $href, $label, $title, $icon, $attributes)
	{
		if (\strlen(Contao\Input::get('tid')))
		{
			$this->toggleVisibility(Contao\Input::get('tid'), (Contao\Input::get('state') == 1), (@func_get_arg(12) ?: null));
			$this->redirect($this->getReferer());
		}

		// Check permissions AFTER checking the tid, so hacking attempts are logged
		if (!$this->User->hasAccess('tl_h4teams::published', 'alexf'))
		{
			return '';
		}

		$href .= '&amp;tid='.$row['id'].'&amp;state='.($row['published'] ? '' : 1);

		if (!$row['published'])
		{
			$icon = 'invisible.svg';
		}

		return '<a href="'.$this->addToUrl($href).'" title="'.Contao\StringUtil::specialchars($title).'"'.$attributes.'>'.Contao\Image::getHtml($icon, $label, 'data-state="' . ($row['published'] ? 1 : 0) . '"').'</a> ';
	}

	/**
	 * Disable/enable a user group
	 *
	 * @param integer              $intId
	 * @param boolean              $blnVisible
	 * @param Contao\DataContainer $dc
	 *
	 * @throws Contao\CoreBundle\Exception\AccessDeniedException
	 */
	public function toggleVisibility($intId, $blnVisible, Contao\DataContainer $dc=null)
	{
		// Set the ID and action
		Contao\Input::setGet('id', $intId);
		Contao\Input::setGet('act', 'toggle');

		if ($dc)
		{
			$dc->id = $intId; // see #8043
		}

		// Trigger the onload_callback
		if (\is_array($GLOBALS['TL_DCA']['tl_h4teams']['config']['onload_callback']))
		{
			foreach ($GLOBALS['TL_DCA']['tl_h4teams']['config']['onload_callback'] as $callback)
			{
				if (\is_array($callback))
				{
					$this->import($callback[0]);
					$this->{$callback[0]}->{$callback[1]}($dc);
				}
				elseif (\is_callable($callback))
				{
					$callback($dc);
				}
			}
		}

		// Check the field access
		if (!$this->User->hasAccess('tl_h4teams::published', 'alexf'))
		{
			throw new Contao\CoreBundle\Exception\AccessDeniedException('Not enough permissions to publish/unpublish event ID ' . $intId . '.');
		}

		// Set the current record
		if ($dc)
		{
			$objRow = $this->Database->prepare("SELECT * FROM tl_h4teams WHERE id=?")
									 ->limit(1)
									 ->execute($intId);

			if ($objRow->numRows)
			{
				$dc->activeRecord = $objRow;
			}
		}

		$objVersions = new Contao\Versions('tl_h4teams', $intId);
		$objVersions->initialize();

		// Trigger the save_callback
		if (\is_array($GLOBALS['TL_DCA']['tl_h4teams']['fields']['published']['save_callback']))
		{
			foreach ($GLOBALS['TL_DCA']['tl_h4teams']['fields']['published']['save_callback'] as $callback)
			{
				if (\is_array($callback))
				{
					$this->import($callback[0]);
					$blnVisible = $this->{$callback[0]}->{$callback[1]}($blnVisible, $dc);
				}
				elseif (\is_callable($callback))
				{
					$blnVisible = $callback($blnVisible, $dc);
				}
			}
		}

		$time = time();

		// Update the database
		$this->Database->prepare("UPDATE tl_h4teams SET tstamp=$time, published='" . ($blnVisible ? '1' : '') . "' WHERE id=?")
					   ->execute($intId);

		if ($dc)
		{
			$dc->activeRecord->tstamp = $time;
			$dc->activeRecord->published = ($blnVisible ? '1' : '');
		}

		// Trigger the onsubmit_callback
		if (\is_array($GLOBALS['TL_DCA']['tl_h4teams']['config']['onsubmit_callback']))
		{
			foreach ($GLOBALS['TL_DCA']['tl_h4teams']['config']['onsubmit_callback'] as $callback)
			{
				if (\is_array($callback))
				{
					$this->import($callback[0]);
					$this->{$callback[0]}->{$callback[1]}($dc);
				}
				elseif (\is_callable($callback))
				{
					$callback($dc);
				}
			}
		}

		$objVersions->create();

		// The onsubmit_callback has triggered scheduleUpdate(), so run generateFeed() now
		$this->generateFeed();
	}
}
