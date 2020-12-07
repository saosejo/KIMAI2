<?php

/*
 * This file is part of the Kimai time-tracking app.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Widget\Type;

use App\Entity\Project;
use App\Entity\Team;
use App\Entity\User;
use App\Repository\ProjectRepository;

class UserTeamProjects extends SimpleWidget implements AuthorizedWidget, UserWidget
{
    /**
     * @var ProjectRepository
     */
    private $repository;

    public function __construct(ProjectRepository $repository)
    {
        $this->setId('UserTeamProjects');
        $this->setTitle('label.my_team_projects');
        $this->setOption('id', '');
        $this->repository = $repository;
    }

    public function getOptions(array $options = []): array
    {
        $options = parent::getOptions($options);

        if (empty($options['id'])) {
            $options['id'] = 'WidgetUserTeamProjects';
        }

        return $options;
    }

    public function getData(array $options = [])
    {
        $options = $this->getOptions($options);
        /** @var User $user */
        $user = $options['user'];
        $projects = [];

        /** @var Team $team */
        foreach ($user->getTeams() as $team) {
            /** @var Project $project */
            foreach ($team->getProjects() as $project) {
                if (!$project->isVisible() || !$project->getCustomer()->isVisible()) {
                    continue;
                }
                $projects[$project->getId()] = $project;
            }
        }

        $stats = [];

        foreach ($projects as $id => $project) {
            if ($project->getBudget() > 0 || $project->getTimeBudget() > 0) {
                $stats[] = $this->repository->getProjectStatistics($project);
            }
        }

        return $stats;
    }

    /**
     * @return string[]
     */
    public function getPermissions(): array
    {
        return ['budget_team_project', 'budget_teamlead_project', 'budget_project'];
    }

    public function setUser(User $user): void
    {
        $this->setOption('user', $user);
    }
}
