<?php

use src\Service\DatabaseService\DatabaseService;

echo generate();

function generate(): string
{
    $dbService = new DatabaseService();
    $logOnBalance = sprintf(
        '%02d:%02d:%02d',
        (int)$_SESSION['user']['balance'] / 3600,
        abs((int)$_SESSION['user']['balance']) / 60 % 60,
        abs((int)$_SESSION['user']['balance']) % 60
    );

    $userPanel = '<table class="user_panel_info">
        <tr>
            <th>Currently logged in as: </th>
            <td>' . $_SESSION['user']['username'] . '</td>
        </tr>
        <tr>
            <th>Log on balance: </th>
            <td>' . $logOnBalance . '</td>
        </tr>';

    if ($_SESSION['user']['privileges'] === true) {
        $userPanel .= '<tr><th><p>Currently showing</p></th></tr><tr><td colspan="2">
                        <select class="user_selection"><option>YOU</option>';
        $teamsPermissions = $dbService->getTeamsPermissions($_SESSION['user_id']);

        $userOptions = null;
        foreach ($teamsPermissions as $teamsPermission) {
            if ($teamsPermission['permission'] !== 'member') {
                foreach ($dbService->getMembersOfTeam($teamsPermission['team_name']) as $user) {
                    $userOptions[$teamsPermission['team_name']][] = $user['username'];
                }

            }
        }

        foreach ($userOptions as $team => $users) {
            $userPanel .= '<optgroup label="' . $team . '">';
            foreach ($users as $user) {
                $userPanel .= '<option>' . $user . '</option>';
            }
            $userPanel .= '</optgroup>';
        }

        $userPanel .= '</select></td></tr>';
    }

    return $userPanel . '</table>';
}



