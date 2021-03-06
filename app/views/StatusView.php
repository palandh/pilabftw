<?php
/**
 * \Elabftw\Elabftw\StatusView
 *
 * @author Nicolas CARPi <nicolas.carpi@curie.fr>
 * @copyright 2012 Nicolas CARPi
 * @see http://www.elabftw.net Official website
 * @license AGPL-3.0
 * @package elabftw
 */
namespace Elabftw\Elabftw;

use \PDO;

/**
 * HTML for the status
 */
class StatusView
{
    /** The PDO object */
    private $pdo;

    /**
     * Constructor
     *
     */
    public function __construct()
    {
        $this->pdo = Db::getConnection();
    }

    /**
     * Output HTML to display the create new status block
     *
     * @return string $html
     */
    public function showCreate()
    {
        $html = "<h3>" . _('Add a new status') . "</h3>";
        $html .= "<ul class='list-group'><li class='list-group-item center'>";
        $html .= "<ul class='list-inline'>";
        $html .= "<li>" . _('Name') . " <input type='text' id='statusName' /></li>";
        $html .= "<li>" . _('Color') . " <input class='colorpicker' type='text' id='statusColor' value='000000' /></li>";
        $html .= "<li><button onClick='statusCreate()' class='button'>" . _('Save') . "</button></li>";
        $html .= "</ul></li></ul>";

        return $html;
    }

    /**
     * Output HTML with all the status
     *
     * @param array $statusArr The output of the read() function
     * @param int $team
     * @return string $html
     */
    public function show($statusArr, $team)
    {
        $html = "<h3>" . _('Edit an existing status') . "</h3>";
        $html .= "<ul class='draggable sortable_status list-group'>";

        foreach ($statusArr as $status) {
            // count the experiments with this status
            // don't allow deletion if experiments with this status exist
            // but instead display a message to explain
            $count_exp_sql = "SELECT COUNT(*) FROM experiments WHERE status = :status AND team = :team";
            $count_exp_req = $this->pdo->prepare($count_exp_sql);
            $count_exp_req->bindParam(':status', $status['id'], PDO::PARAM_INT);
            $count_exp_req->bindParam(':team', $team, PDO::PARAM_INT);
            $count_exp_req->execute();
            $count = $count_exp_req->fetchColumn();

            $html .= "<li id='" . $status['id'] . "' class='list-group-item center'>";


            $html .= "<ul class='list-inline'>";

            $html .= "<li>" . _('Name') . " <input required type='text' id='statusName_" . $status['id'] . "' value='" . $status['name'] . "' /></li>";
            $html .= "<li style='color:#" . $status['color'] . "'>" . _('Color') . " <input class='colorpicker' type='text' maxlength='6' id='statusColor_" . $status['id'] . "' value='" . $status['color'] . "' />";
            $html .= "</li>";
            $html .= "<li>" . _('Default status') . " <input type='checkbox' id='statusDefault_" . $status['id'] . "'";
            // check the box if the status is already default
            if ($status['is_default'] == 1) {
                $html .= " checked";
            }
            $html .= "></li>";


            $html .= "<li><button onClick='statusUpdate(" . $status['id'] . ")' class='button'>" . _('Save') . "</button></li>";

            $html .= "<li><button class='button' ";
            if ($count == 0) {
                $html .= "onClick=\"deleteThis('" . $status['id'] . "','status', 'admin.php?tab=3')\"";
            } else {
                $html .= "onClick=\"alert('" . _('Remove all experiments with this status before deleting this status.') . "')\"";
            }
            $html .= ">" . _('Delete') . "</button></li>";

            $html .= "</ul></li>";
        }
        $html .= "</ul>";

        return $html;
    }
}
