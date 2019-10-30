<section class="content__side">
    <h2 class="content__side-heading">Проекты</h2>

    <nav class="main-navigation">
        <ul class="main-navigation__list">
            <?php foreach ($projects as $project): ?>
            <li class="main-navigation__list-item <?= ($project['id'] === $current_project_id) ? 'main-navigation__list-item--active' : ''; ?>">
                <a class="main-navigation__list-item-link"
                <?= ($project['id'] === $current_project_id) ? '' : 'href="/?'.build_query(['project_id' => $project['id']]).'"'; ?>
                ><?= htmlspecialchars($project['title']); ?></a>
                <span class="main-navigation__list-item-count"><?= count_number_of_tasks($tasks, $project['title']); ?></span>
            </li>
            <?php endforeach; ?>
        </ul>
    </nav>

    <a class="button button--transparent button--plus content__side-button"
        href="add-project.php" target="project_add">Добавить проект</a>
</section>
