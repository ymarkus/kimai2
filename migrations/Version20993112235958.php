<?php

declare(strict_types=1);

/*
 * This file is part of the Kimai time-tracking app.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * @version 2.0
 */
final class Version20993112235958 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Necessary changes for 2.0';
    }

    public function up(Schema $schema): void
    {
        $customers = $schema->getTable('kimai2_customers');

        // clean up column length for strict mode
        $customers->getColumn('company')->setLength(100);
        $customers->getColumn('contact')->setLength(100);
        $customers->getColumn('phone')->setLength(30);
        $customers->getColumn('fax')->setLength(30);
        $customers->getColumn('mobile')->setLength(30);
        $customers->getColumn('homepage')->setLength(100);
        $customers->getColumn('email')->setLength(75);

        $users = $schema->getTable('kimai2_users');
        if (!$users->hasColumn('totp_secret')) {
            $users->addColumn('totp_secret', 'string', ['notnull' => false, 'default' => null]);
            $users->addColumn('totp_enabled', 'boolean', ['notnull' => true, 'default' => false]);
        }

        // new invoice features
        if (!$customers->hasColumn('invoice_template_id')) {
            $customers->addColumn('invoice_template_id', 'integer', ['notnull' => false, 'default' => null]);
            $customers->addForeignKeyConstraint('kimai2_invoice_templates', ['invoice_template_id'], ['id'], ['onDelete' => 'SET NULL'], 'FK_5A97604412946D8B');
            $customers->addIndex(['invoice_template_id'], 'IDX_5A97604412946D8B');
        }

        if (!$customers->hasColumn('invoice_text')) {
            $customers->addColumn('invoice_text', 'text', ['notnull' => false, 'default' => null]);
        }

        $timesheet = $schema->getTable('kimai2_timesheet');
        if (!$timesheet->hasIndex('IDX_4F60C6B1415614018D93D649')) {
            $timesheet->addIndex(['end_time', 'user'], 'IDX_4F60C6B1415614018D93D649');
        }

        $this->addSql("UPDATE kimai2_invoice_templates SET `language` = 'en' WHERE `language` IS NULL");
        $this->addSql("UPDATE kimai2_user_preferences SET `name` = 'timesheet_daily_stats' WHERE `name` = 'timesheet.daily_stats'");
        $this->addSql("UPDATE kimai2_user_preferences SET `name` = 'collapsed_sidebar' WHERE `name` = 'theme.collapsed_sidebar'");
        $this->addSql("UPDATE kimai2_user_preferences SET `name` = 'layout' WHERE `name` = 'theme.layout'");
        $this->addSql("UPDATE kimai2_user_preferences SET `name` = 'login_initial_view' WHERE `name` = 'login.initial_view'");
        $this->addSql("UPDATE kimai2_user_preferences SET `name` = 'calendar_initial_view' WHERE `name` = 'calendar.initial_view'");
        $this->addSql("UPDATE kimai2_user_preferences SET `name` = 'export_decimal' WHERE `name` = 'timesheet.export_decimal'");
        $this->addSql("UPDATE kimai2_user_preferences SET `name` = 'update_browser_title' WHERE `name` = 'theme.update_browser_title'");
        $this->addSql("UPDATE kimai2_configuration SET `value` = '15' WHERE `name` = 'timesheet.time_increment' and `value` = '0'");
    }

    public function down(Schema $schema): void
    {
        $customers = $schema->getTable('kimai2_customers');
        $customers->getColumn('company')->setLength(255);
        $customers->getColumn('contact')->setLength(255);
        $customers->getColumn('phone')->setLength(255);
        $customers->getColumn('fax')->setLength(255);
        $customers->getColumn('mobile')->setLength(255);
        $customers->getColumn('homepage')->setLength(255);

        $customers->removeForeignKey('FK_5A97604412946D8B');
        $customers->dropIndex('IDX_5A97604412946D8B');
        $customers->dropColumn('invoice_template_id');
        $customers->dropColumn('invoice_text');

        $timesheet = $schema->getTable('kimai2_timesheet');
        if ($timesheet->hasIndex('IDX_4F60C6B1415614018D93D649')) {
            $timesheet->dropIndex('IDX_4F60C6B1415614018D93D649');
        }

        $this->addSql("UPDATE kimai2_user_preferences SET `name` = 'theme.collapsed_sidebar' WHERE `name` = 'collapsed_sidebar'");
        $this->addSql("UPDATE kimai2_user_preferences SET `name` = 'theme.layout' WHERE `name` = 'theme_layout'");
        $this->addSql("UPDATE kimai2_user_preferences SET `name` = 'calendar.initial_view' WHERE `name` = 'calendar_initial_view'");
        $this->addSql("UPDATE kimai2_user_preferences SET `name` = 'login.initial_view' WHERE `name` = 'login_initial_view'");
        $this->addSql("UPDATE kimai2_user_preferences SET `name` = 'timesheet.daily_stats' WHERE `name` = 'timesheet_daily_stats'");
        $this->addSql("UPDATE kimai2_user_preferences SET `name` = 'timesheet.export_decimal' WHERE `name` = 'export_decimal'");
        $this->addSql("UPDATE kimai2_user_preferences SET `name` = 'theme.update_browser_title' WHERE `name` = 'update_browser_title'");
    }
}
