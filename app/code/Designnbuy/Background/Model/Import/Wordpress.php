<?php
/**
 * Copyright © 2017 Ajay Makwana (ajay.makwana@rightwaysolution.com). All rights reserved.
 * See LICENSE.txt for license details (http://opensource.org/licenses/osl-3.0.php).
 *
 * Life is a code
 */

namespace Designnbuy\Background\Model\Import;

/**
 * Wordpress import model
 */
class Wordpress extends AbstractImport
{
    protected $_requiredFields = ['dbname', 'uname', 'pwd', 'dbhost', 'prefix', 'store_id'];

    public function execute()
    {
        $con = $this->_connect = mysqli_connect(
            $this->getData('dbhost'),
            $this->getData('uname'),
            $this->getData('pwd'),
            $this->getData('dbname')
        );

        if (mysqli_connect_errno()) {
            throw new \Exception("Failed connect to wordpress database", 1);
        }

        $_pref = mysqli_real_escape_string($con, $this->getData('prefix'));

        $categories = [];
        $oldCategories = [];

        /* Import categories */
        $sql = 'SELECT
                    t.term_id as old_id,
                    t.name as title,
                    t.slug as identifier,
                    tt.parent as parent_id
                FROM '.$_pref.'terms t
                LEFT JOIN '.$_pref.'term_taxonomy tt on t.term_id = tt.term_id
                WHERE tt.taxonomy = "category" AND t.slug <> "uncategorized"';

        $result = $this->_mysqliQuery($sql);
        while ($data = mysqli_fetch_assoc($result)) {
            /* Prepare category data */
            foreach (['title', 'identifier'] as $key) {
                $data[$key] = utf8_encode($data[$key]);
            }

            $data['store_ids'] = [$this->getStoreId()];
            $data['is_active'] = 1;
            $data['position'] = 0;
            $data['path'] = 0;
            $data['identifier'] = trim(strtolower($data['identifier']));
            if (strlen($data['identifier']) == 1) {
                $data['identifier'] .= $data['identifier'];
            }

            $category = $this->_categoryFactory->create();
            try {
                /* Initial saving */
                $category->setData($data)->save();
                $this->_importedCategoriesCount++;
                $categories[$category->getId()] = $category;
                $oldCategories[$category->getOldId()] = $category;
            } catch (\Magento\Framework\Exception\LocalizedException $e) {
                unset($category);
                $this->_skippedCategories[] = $data['title'];
            }
        }

        /* Reindexing parent categories */
        foreach ($categories as $ct) {
            if ($oldParentId = $ct->getData('parent_id')) {
                if (isset($oldCategories[$oldParentId])) {
                    $ct->setPath(
                        $parentId = $oldCategories[$oldParentId]->getId()
                    );
                }
            }
        }

        for ($i = 0; $i < 4; $i++) {
            $changed = false;
            foreach ($categories as $ct) {
                if ($ct->getPath()) {
                    $parentId = explode('/', $ct->getPath())[0];
                    $pt = $categories[$parentId];
                    if ($pt->getPath()) {
                        $ct->setPath($pt->getPath() . '/'. $ct->getPath());
                        $changed = true;
                    }
                }
            }

            if (!$changed) {
                break;
            }
        }
        /* end*/

        foreach($categories as $ct) {
            /* Final saving */
            $ct->save();
        }

        /* Import tags */
        $tags = [];
        $oldTags = [];

        $sql = 'SELECT
                    t.term_id as old_id,
                    t.name as title,
                    t.slug as identifier,
                    tt.parent as parent_id
                FROM '.$_pref.'terms t
                LEFT JOIN '.$_pref.'term_taxonomy tt on t.term_id = tt.term_id
                WHERE tt.taxonomy = "background_tag" AND t.slug <> "uncategorized"';

        $result = $this->_mysqliQuery($sql);
        while ($data = mysqli_fetch_assoc($result)) {
            /* Prepare tag data */
            foreach (['title', 'identifier'] as $key) {
                $data[$key] = utf8_encode($data[$key]);
            }

            $data['identifier'] = trim(strtolower($data['identifier']));
            if (strlen($data['identifier']) == 1) {
                $data['identifier'] .= $data['identifier'];
            }

            $tag = $this->_tagFactory->create();
            try {
                /* Initial saving */
                $tag->setData($data)->save();
                $this->_importedTagsCount++;
                $tags[$tag->getId()] = $tag;
                $oldTags[$tag->getOldId()] = $tag;
            } catch (\Magento\Framework\Exception\LocalizedException $e) {
                unset($tag);
                $this->_skippedTags[] = $data['title'];
            }
        }

        /* Import backgrounds */
        $sql = 'SELECT * FROM '.$_pref.'backgrounds WHERE `background_type` = "background"';
        $result = $this->_mysqliQuery($sql);

        while ($data = mysqli_fetch_assoc($result)) {

            /* find background categories*/
            $backgroundCategories = [];

            $sql = 'SELECT tt.term_id as term_id FROM '.$_pref.'term_relationships tr
                    LEFT JOIN '.$_pref.'term_taxonomy tt ON tr.term_taxonomy_id = tt.term_taxonomy_id
                    WHERE tr.`object_id` = "'.$data['ID'].'" AND tt.taxonomy = "category"';

            $result2 = $this->_mysqliQuery($sql);
            while ($data2 = mysqli_fetch_assoc($result2)) {
                $oldTermId = $data2['term_id'];
                if (isset($oldCategories[$oldTermId])) {
                    $backgroundCategories[] = $oldCategories[$oldTermId]->getId();
                }
            }

            /* find background tags*/
            $backgroundTags = [];

            $sql = 'SELECT tt.term_id as term_id FROM '.$_pref.'term_relationships tr
                    LEFT JOIN '.$_pref.'term_taxonomy tt ON tr.term_taxonomy_id = tt.term_taxonomy_id
                    WHERE tr.`object_id` = "'.$data['ID'].'" AND tt.taxonomy = "background_tag"';

            $result2 = $this->_mysqliQuery($sql);
            while ($data2 = mysqli_fetch_assoc($result2)) {
                $oldTermId = $data2['term_id'];
                if (isset($oldTags[$oldTermId])) {
                    $backgroundTags[] = $oldTags[$oldTermId]->getId();
                }
            }

            $data['image'] = '';

            $sql = 'SELECT wm2.meta_value as image
                FROM
                    '.$_pref.'backgrounds p1
                LEFT JOIN
                    '.$_pref.'backgroundmeta wm1
                    ON (
                        wm1.background_id = p1.id
                        AND wm1.meta_value IS NOT NULL
                        AND wm1.meta_key = "_thumbnail_id"
                    )
                LEFT JOIN
                    '.$_pref.'backgroundmeta wm2
                    ON (
                        wm1.meta_value = wm2.background_id
                        AND wm2.meta_key = "_wp_attached_file"
                        AND wm2.meta_value IS NOT NULL
                    )
                WHERE
                    p1.ID="'.$data['ID'].'"
                    AND p1.background_type="background"
                ORDER BY
                    p1.background_date DESC';

            $result2 = $this->_mysqliQuery($sql);
            if ($data2 = mysqli_fetch_assoc($result2)) {
                if ($data2['image']) {
                    $data['image'] = \Designnbuy\Background\Model\Background::BASE_MEDIA_PATH . '/' . $data2['image'];
                }
            }

            /* Prepare background data */
            foreach (['background_title', 'background_name', 'background_content'] as $key) {
                $data[$key] = utf8_encode($data[$key]);
            }

            $creationTime = strtotime($data['background_date_gmt']);
            $data = [
                'store_ids' => [$this->getStoreId()],
                'title' => $data['background_title'],
                'meta_keywords' => '',
                'meta_description' => '',
                'identifier' => $data['background_name'],
                'content_heading' => '',
                'content' => str_replace('<!--more-->', '<!-- pagebreak -->', $data['background_content']),
                'creation_time' => $creationTime,
                'update_time' => strtotime($data['background_modified_gmt']),
                'publish_time' => $creationTime,
                'is_active' => (int)($data['background_status'] == 'publish'),
                'categories' => $backgroundCategories,
                'tags' => $backgroundTags,
                'image' => $data['image'],
            ];
            $data['identifier'] = trim(strtolower($data['identifier']));
            if (strlen($data['identifier']) == 1) {
                $data['identifier'] .= $data['identifier'];
            }

            $background = $this->_backgroundFactory->create();
            try {
                /* Background saving */
                $background->setData($data)->save();
                $this->_importedBackgroundsCount++;
            } catch (\Magento\Framework\Exception\LocalizedException $e) {
                $this->_skippedBackgrounds[] = $data['title'];
            }

            unset($background);
        }
        /* end */

        mysqli_close($con);
    }

}
