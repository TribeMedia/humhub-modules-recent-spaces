<?php

/**
 * Recent Spaces Controller defines actions for recent spaces.
 *
 * @package humhub.modules.recent-spaces.controllers
 * @author Ole Rößner
 */
class RecentSpacesController extends Controller
{

  /**
   *
   * @return array action filters
   */
  public function filters()
  {
    return array(
      'accessControl'
    ); // perform access control for CRUD operations
  }

  /**
   * Specifies the access control rules.
   * This method is used by the 'accessControl' filter.
   *
   * @return array access control rules
   */
  public function accessRules()
  {
    return array(
      array('allow', // allow authenticated user to perform 'create' and 'update' actions
        'users' => array('@'),
      ),
      array('deny', // deny all users
        'users' => array('*'),
      ),
    );
  }

  /**
   * Shows list of recent spaces with number of members.
   */
  public function actionList()
  {

    $query = 'SELECT s.id as space_id, COUNT(sm.user_id) as num_members
                    FROM space s
                    JOIN space_membership sm
                        ON sm.space_id = s.id
                    WHERE s.visibility <> :space_visbility
                    GROUP BY s.id
                    ORDER BY s.created_at DESC;';

    $recentSpaces = Yii::app()->db->createCommand($query)->queryAll(true, array('space_visbility' => Space::VISIBILITY_NONE));
    $dataProvider = new CArrayDataProvider($recentSpaces, array(
      'id' => 'space_id',
      'pagination' => array(
        'pageSize' => 5
      )
    ));

    $recentSpaces = $dataProvider->getData();
    $pages = $dataProvider->getPagination();

    $output = $this->renderPartial('list', array(
      'pages' => $pages,
      'spaces' => $recentSpaces
    ));
    echo $output;
    Yii::app()->end();
  }
}

?>
