<?php

declare(strict_types=1);

/**
 * Created by PhpStorm.
 * User: thomasvoggenreiter
 * Date: 30.10.17
 * Time: 13:37
 */

namespace Resources\contao\dca;

use Contao\Backend;
use Contao\DataContainer;
use Contao\LayoutModel;
use Contao\PageModel;
use Contao\StringUtil;
use Contao\ThemeModel;
use Dreibein\ArticleConfigBundle\Model\DreibeinThemeModel;

$table = 'tl_article';

$GLOBALS['TL_DCA'][$table]['palettes']['default'] = str_replace("author", "author;{dreibein_article_config_legend},dreibein_article_config_class,dreibein_article_config_space,dreibein_article_config_background", $GLOBALS['TL_DCA'][$table]['palettes']['default']);

$GLOBALS['TL_DCA'][$table]['fields']['dreibein_article_config_class'] = [
    'label' => &$GLOBALS['TL_LANG'][$table]['dreibein_article_config_class'],
    'exclude' => 'true',
    'inputType' => 'select',
    'options' => ['no-inside'],
    'reference' => &$GLOBALS['TL_LANG'][$table]['dreibein_article_config_class'],
    'eval' => ['tl_class' => 'w50', 'includeBlankOption' => true],
    'sql' => "varchar(10) NOT NULL default ''"
];

$GLOBALS['TL_DCA'][$table]['fields']['dreibein_article_config_space'] = [
    'label' => &$GLOBALS['TL_LANG'][$table]['dreibein_article_config_space'],
    'exclude' => 'true',
    'inputType' => 'select',
    'options' => ['space-p', 'space-pt', 'space-pb'],
    'reference' => &$GLOBALS['TL_LANG'][$table]['dreibein_article_config_space'],
    'eval' => ['tl_class' => 'w50', 'includeBlankOption' => true],
    'sql' => "varchar(10) NOT NULL default ''"
];

$GLOBALS['TL_DCA'][$table]['fields']['dreibein_article_config_background'] = [
    'label' => &$GLOBALS['TL_LANG'][$table]['dreibein_article_config_background'],
    'exclude' => 'true',
    'inputType' => 'select',
    'options_callback' => [tl_dreibein_article::class, 'getColor'],
    'eval' => ['tl_class' => 'w50', 'includeBlankOption' => true],
    'sql' => "varchar(60) NOT NULL default ''"
];

/**
 * Class tl_dreibein_article
 * @package Resources\contao\dca
 */
class tl_dreibein_article extends Backend
{
    /**
     * @param DataContainer $dc
     *
     * @return array
     */
    public function getColor(DataContainer $dc): array
    {
        $colorArray = [];

        // get layoutId from tl_page
        $layoutId = $dc->activeRecord->layout;

        // page has no own layout (look for parent pages)
        if (!$layoutId) {
            $page = PageModel::findById((int)$dc->activeRecord->pid);
            while ($page && (int)$page->layout === 0) {
                $page = PageModel::findById((int)$page->pid);
            }
            $layoutId = $page->layout;
        }

        $layout = LayoutModel::findById($layoutId);
        if ($layout !== null) {
            /** @var DreibeinThemeModel $theme */
            $theme = ThemeModel::findById($layout->pid);
            if ($theme) {
                $colors = StringUtil::deserialize($theme->dreibein_theme_colors);
                foreach ($colors as $color) {
                    $colorArray[$color['key']] = $color['value'];
                }
            }
        }
        return $colorArray;
    }
}
