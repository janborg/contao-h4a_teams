<?php
/**
 * Table tl_h4aseason
 */
$GLOBALS ['TL_DCA'] ['tl_h4aseason'] = [
    // Config
    'config' => [
      'dataContainer'               => 'Table',
	    'ctable'                      => array('tl_h4ateams'),
      'switchToEdit'                => true,
      'enableVersioning'            => true,
      'markAsCopy'                  => 'title',
      'sql' => array
		    (
          'keys' => array
          (
            'id' => 'primary'
          )
        )
    ],
    'list' => array
    	(
    		'sorting' => array
    		(
    			'mode'                    => 1,
    			'fields'                  => array('title'),
    			'flag'                    => 1,
    			'panelLayout'             => 'filter;search,limit'
    		),
    		'label' => array
    		(
    			'fields'                  => array('title'),
    			'format'                  => '%s'
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
    				'label'               => &$GLOBALS['TL_LANG']['tl_h4aseason']['edit'],
    				'href'                => 'table=tl_h4ateams',
    				'icon'                => 'edit.svg'
    			),
    			'editheader' => array
    			(
    				'label'               => &$GLOBALS['TL_LANG']['tl_h4aseason']['editheader'],
    				'href'                => 'act=edit',
    				'icon'                => 'header.svg',
    				'button_callback'     => array('tl_h4aseason', 'editHeader')
    			),
    			'copy' => array
    			(
    				'label'               => &$GLOBALS['TL_LANG']['tl_h4aseason']['copy'],
    				'href'                => 'act=copy',
    				'icon'                => 'copy.svg',
    				'button_callback'     => array('tl_h4aseason', 'copySeason')
    			),
    			'delete' => array
    			(
    				'label'               => &$GLOBALS['TL_LANG']['tl_h4aseason']['delete'],
    				'href'                => 'act=delete',
    				'icon'                => 'delete.svg',
    				'attributes'          => 'onclick="if(!confirm(\'' . $GLOBALS['TL_LANG']['MSC']['deleteConfirm'] . '\'))return false;Backend.getScrollOffset()"',
    				'button_callback'     => array('tl_h4aseason', 'deleteSeason')
    			),
    			'show' => array
    			(
    				'label'               => &$GLOBALS['TL_LANG']['tl_h4aseason']['show'],
    				'href'                => 'act=show',
    				'icon'                => 'show.svg'
    			)
    		)
    	),
    // Palettes
    'palettes' => array
    (
      'default' => '{title_legend},title, alias'
    ),
    // Fields
    'fields' => array
    (
    		'id' => array
    		(
    			'sql'                     => "int(10) unsigned NOT NULL auto_increment"
    		),
    		'tstamp' => array
    		(
    			'sql'                     => "int(10) unsigned NOT NULL default '0'"
    		),
    		'title' => array
    		(
    			'label'                   => &$GLOBALS['TL_LANG']['tl_h4aseason']['title'],
    			'exclude'                 => true,
    			'search'                  => true,
    			'inputType'               => 'text',
    			'eval'                    => array('mandatory'=>true, 'maxlength'=>255, 'tl_class'=>'w50'),
    			'sql'                     => "varchar(255) NOT NULL default ''"
    		),
        'alias' => array
        (
          'label'                   => &$GLOBALS['TL_LANG']['tl_h4aseason']['alias'],
          'exclude'                 => true,
          'search'                  => true,
          'inputType'               => 'text',
          'eval'                    => array('rgxp'=>'alias', 'doNotCopy'=>true, 'unique'=>true, 'maxlength'=>128, 'tl_class'=>'w50 clr'),
          'save_callback' => array
          (
            array('tl_h4aseason', 'generateAlias')
          ),
          'sql'                     => "varchar(128) BINARY NOT NULL default ''"
        ),
    )
];

/**
 * Provide miscellaneous methods that are used by the data configuration array.
 *
 * @property Contao\Calendar $Season
 *
 * @author Leo Feyer <https://github.com/leofeyer>
 */
class tl_h4aseason extends Contao\Backend
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
	 * Check permissions to edit table tl_h4aseason
	 *
	 * @throws Contao\CoreBundle\Exception\AccessDeniedException
	 */
	public function checkPermission()
	{
		$bundles = Contao\System::getContainer()->getParameter('kernel.bundles');

		// HOOK: comments extension required
		if (!isset($bundles['ContaoCommentsBundle']))
		{
			unset($GLOBALS['TL_DCA']['tl_h4aseason']['fields']['allowComments']);
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

		$GLOBALS['TL_DCA']['tl_h4aseason']['list']['sorting']['root'] = $root;

		// Check permissions to add calendars
		if (!$this->User->hasAccess('create', 'calendarp'))
		{
			$GLOBALS['TL_DCA']['tl_h4aseason']['config']['closed'] = true;
			$GLOBALS['TL_DCA']['tl_h4aseason']['config']['notCreatable'] = true;
			$GLOBALS['TL_DCA']['tl_h4aseason']['config']['notCopyable'] = true;
		}

		// Check permissions to delete calendars
		if (!$this->User->hasAccess('delete', 'calendarp'))
		{
			$GLOBALS['TL_DCA']['tl_h4aseason']['config']['notDeletable'] = true;
		}

		/** @var Symfony\Component\HttpFoundation\Session\SessionInterface $objSession */
		$objSession = Contao\System::getContainer()->get('session');

		// Check current action
		switch (Contao\Input::get('act'))
		{
			case 'select':
				// Allow
				break;

			case 'create':
				if (!$this->User->hasAccess('create', 'calendarp'))
				{
					throw new Contao\CoreBundle\Exception\AccessDeniedException('Not enough permissions to create calendars.');
				}
				break;

			case 'edit':
			case 'copy':
			case 'delete':
			case 'show':
				if (!\in_array(Contao\Input::get('id'), $root) || (Contao\Input::get('act') == 'delete' && !$this->User->hasAccess('delete', 'calendarp')))
				{
					throw new Contao\CoreBundle\Exception\AccessDeniedException('Not enough permissions to ' . Contao\Input::get('act') . ' calendar ID ' . Contao\Input::get('id') . '.');
				}
				break;

			case 'editAll':
			case 'deleteAll':
			case 'overrideAll':
			case 'copyAll':
				$session = $objSession->all();
				if (Contao\Input::get('act') == 'deleteAll' && !$this->User->hasAccess('delete', 'calendarp'))
				{
					$session['CURRENT']['IDS'] = array();
				}
				else
				{
					$session['CURRENT']['IDS'] = array_intersect((array) $session['CURRENT']['IDS'], $root);
				}
				$objSession->replace($session);
				break;

			default:
				if (\strlen(Contao\Input::get('act')))
				{
					throw new Contao\CoreBundle\Exception\AccessDeniedException('Not enough permissions to ' . Contao\Input::get('act') . ' calendars.');
				}
				break;
		}
	}

	/**
	 * Add the new calendar to the permissions
	 *
	 * @param $insertId
	 */
	public function adjustPermissions($insertId)
	{
		// The oncreate_callback passes $insertId as second argument
		if (\func_num_args() == 4)
		{
			$insertId = func_get_arg(1);
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

		// The calendar is enabled already
		if (\in_array($insertId, $root))
		{
			return;
		}

		/** @var Symfony\Component\HttpFoundation\Session\Attribute\AttributeBagInterface $objSessionBag */
		$objSessionBag = Contao\System::getContainer()->get('session')->getBag('contao_backend');

		$arrNew = $objSessionBag->get('new_records');

		if (\is_array($arrNew['tl_h4aseason']) && \in_array($insertId, $arrNew['tl_h4aseason']))
		{
			// Add the permissions on group level
			if ($this->User->inherit != 'custom')
			{
				$objGroup = $this->Database->execute("SELECT id, calendars, calendarp FROM tl_user_group WHERE id IN(" . implode(',', array_map('\intval', $this->User->groups)) . ")");

				while ($objGroup->next())
				{
					$arrCalendarp = Contao\StringUtil::deserialize($objGroup->calendarp);

					if (\is_array($arrCalendarp) && \in_array('create', $arrCalendarp))
					{
						$arrCalendars = Contao\StringUtil::deserialize($objGroup->calendars, true);
						$arrCalendars[] = $insertId;

						$this->Database->prepare("UPDATE tl_user_group SET calendars=? WHERE id=?")
									   ->execute(serialize($arrCalendars), $objGroup->id);
					}
				}
			}

			// Add the permissions on user level
			if ($this->User->inherit != 'group')
			{
				$objUser = $this->Database->prepare("SELECT calendars, calendarp FROM tl_user WHERE id=?")
										   ->limit(1)
										   ->execute($this->User->id);

				$arrCalendarp = Contao\StringUtil::deserialize($objUser->calendarp);

				if (\is_array($arrCalendarp) && \in_array('create', $arrCalendarp))
				{
					$arrCalendars = Contao\StringUtil::deserialize($objUser->calendars, true);
					$arrCalendars[] = $insertId;

					$this->Database->prepare("UPDATE tl_user SET calendars=? WHERE id=?")
								   ->execute(serialize($arrCalendars), $this->User->id);
				}
			}

			// Add the new element to the user object
			$root[] = $insertId;
			$this->User->calendars = $root;
		}
	}

  /**
   * Auto-generate the season alias if it has not been set yet
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
      return $this->Database->prepare("SELECT id FROM tl_h4aseason WHERE alias=? AND id!=?")->execute($alias, $dc->id)->numRows > 0;
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
	 * Return the edit header button
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
	public function editHeader($row, $href, $label, $title, $icon, $attributes)
	{
		return $this->User->canEditFieldsOf('tl_h4aseason') ? '<a href="'.$this->addToUrl($href.'&amp;id='.$row['id']).'" title="'.Contao\StringUtil::specialchars($title).'"'.$attributes.'>'.Contao\Image::getHtml($icon, $label).'</a> ' : Contao\Image::getHtml(preg_replace('/\.svg/i', '_.svg', $icon)).' ';
	}

	/**
	 * Return the copy calendar button
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
	public function copySeason($row, $href, $label, $title, $icon, $attributes)
	{
		return $this->User->hasAccess('create', 'calendarp') ? '<a href="'.$this->addToUrl($href.'&amp;id='.$row['id']).'" title="'.Contao\StringUtil::specialchars($title).'"'.$attributes.'>'.Contao\Image::getHtml($icon, $label).'</a> ' : Contao\Image::getHtml(preg_replace('/\.svg/i', '_.svg', $icon)).' ';
	}

	/**
	 * Return the delete season button
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
	public function deleteSeason($row, $href, $label, $title, $icon, $attributes)
	{
		return $this->User->hasAccess('delete', 'calendarp') ? '<a href="'.$this->addToUrl($href.'&amp;id='.$row['id']).'" title="'.Contao\StringUtil::specialchars($title).'"'.$attributes.'>'.Contao\Image::getHtml($icon, $label).'</a> ' : Contao\Image::getHtml(preg_replace('/\.svg/i', '_.svg', $icon)).' ';
	}
}
