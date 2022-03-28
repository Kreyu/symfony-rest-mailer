<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20220307132302 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Initial version migration (OAuth2, scoping and mailer functionality)';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('CREATE TABLE IF NOT EXISTS mailer_attachment (id INT AUTO_INCREMENT NOT NULL, message_id INT NOT NULL, name VARCHAR(255) DEFAULT NULL, content_type VARCHAR(255) DEFAULT NULL, content_id VARCHAR(255) DEFAULT NULL, uuid BINARY(16) NOT NULL COMMENT \'(DC2Type:uuid)\', created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', UNIQUE INDEX UNIQ_7C7BB36D17F50A6 (uuid), INDEX IDX_7C7BB36537A1329 (message_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE IF NOT EXISTS mailer_message (id INT AUTO_INCREMENT NOT NULL, project_id INT NOT NULL, uuid BINARY(16) NOT NULL COMMENT \'(DC2Type:uuid)\', subject VARCHAR(255) DEFAULT NULL, `from` JSON NOT NULL, `to` JSON NOT NULL, cc JSON NOT NULL, bcc JSON NOT NULL, reply_to JSON NOT NULL, body LONGTEXT NOT NULL, charset VARCHAR(255) DEFAULT NULL, date DATETIME DEFAULT NULL, priority INT DEFAULT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', sender_address VARCHAR(255) DEFAULT NULL, sender_name VARCHAR(255) DEFAULT NULL, return_path_address VARCHAR(255) DEFAULT NULL, return_path_name VARCHAR(255) DEFAULT NULL, UNIQUE INDEX UNIQ_9880FD04D17F50A6 (uuid), INDEX IDX_9880FD04166D1F9C (project_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE IF NOT EXISTS mailer_transaction (id INT AUTO_INCREMENT NOT NULL, message_id INT NOT NULL, uuid BINARY(16) NOT NULL COMMENT \'(DC2Type:uuid)\', transport_message_id VARCHAR(255) DEFAULT NULL, transport_debug LONGTEXT DEFAULT NULL, exception_class VARCHAR(255) DEFAULT NULL, exception_message VARCHAR(255) DEFAULT NULL, exception_code VARCHAR(255) DEFAULT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', UNIQUE INDEX UNIQ_E140622ED17F50A6 (uuid), INDEX IDX_E140622E537A1329 (message_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE IF NOT EXISTS oauth2_access_token (identifier CHAR(80) NOT NULL, client VARCHAR(80) NOT NULL, expiry DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', user_identifier VARCHAR(128) DEFAULT NULL, scopes TEXT DEFAULT NULL COMMENT \'(DC2Type:oauth2_scope)\', revoked TINYINT(1) NOT NULL, INDEX IDX_454D9673C7440455 (client), PRIMARY KEY(identifier)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE IF NOT EXISTS oauth2_authorization_code (identifier CHAR(80) NOT NULL, client VARCHAR(80) NOT NULL, expiry DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', user_identifier VARCHAR(128) DEFAULT NULL, scopes TEXT DEFAULT NULL COMMENT \'(DC2Type:oauth2_scope)\', revoked TINYINT(1) NOT NULL, INDEX IDX_509FEF5FC7440455 (client), PRIMARY KEY(identifier)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE IF NOT EXISTS oauth2_client (identifier VARCHAR(80) NOT NULL, name VARCHAR(128) NOT NULL, secret VARCHAR(128) DEFAULT NULL, redirect_uris TEXT DEFAULT NULL COMMENT \'(DC2Type:oauth2_redirect_uri)\', grants TEXT DEFAULT NULL COMMENT \'(DC2Type:oauth2_grant)\', scopes TEXT DEFAULT NULL COMMENT \'(DC2Type:oauth2_scope)\', active TINYINT(1) NOT NULL, allow_plain_text_pkce TINYINT(1) DEFAULT \'0\' NOT NULL, uuid BINARY(16) NOT NULL COMMENT \'(DC2Type:uuid)\', created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', UNIQUE INDEX UNIQ_669FF9C9D17F50A6 (uuid), PRIMARY KEY(identifier)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE IF NOT EXISTS oauth2_client_project (oauth2_client_id VARCHAR(80) NOT NULL, project_id INT NOT NULL, INDEX IDX_35B634FE5DF1BCB8 (oauth2_client_id), INDEX IDX_35B634FE166D1F9C (project_id), PRIMARY KEY(oauth2_client_id, project_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE IF NOT EXISTS oauth2_refresh_token (identifier CHAR(80) NOT NULL, access_token CHAR(80) DEFAULT NULL, expiry DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', revoked TINYINT(1) NOT NULL, INDEX IDX_4DD90732B6A2DD68 (access_token), PRIMARY KEY(identifier)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE IF NOT EXISTS project (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, uuid BINARY(16) NOT NULL COMMENT \'(DC2Type:uuid)\', created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', mailer_configuration_dsn VARCHAR(255) DEFAULT NULL, UNIQUE INDEX UNIQ_2FB3D0EED17F50A6 (uuid), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE IF NOT EXISTS messenger_messages (id BIGINT AUTO_INCREMENT NOT NULL, body LONGTEXT NOT NULL, headers LONGTEXT NOT NULL, queue_name VARCHAR(255) NOT NULL, created_at DATETIME NOT NULL, available_at DATETIME NOT NULL, delivered_at DATETIME DEFAULT NULL, INDEX IDX_75EA56E016BA31DB (delivered_at), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE mailer_attachment ADD CONSTRAINT FK_7C7BB36537A1329 FOREIGN KEY (message_id) REFERENCES mailer_message (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE mailer_message ADD CONSTRAINT FK_9880FD04166D1F9C FOREIGN KEY (project_id) REFERENCES project (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE mailer_transaction ADD CONSTRAINT FK_E140622E537A1329 FOREIGN KEY (message_id) REFERENCES mailer_message (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE oauth2_access_token ADD CONSTRAINT FK_454D9673C7440455 FOREIGN KEY (client) REFERENCES oauth2_client (identifier) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE oauth2_authorization_code ADD CONSTRAINT FK_509FEF5FC7440455 FOREIGN KEY (client) REFERENCES oauth2_client (identifier) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE oauth2_client_project ADD CONSTRAINT FK_35B634FE5DF1BCB8 FOREIGN KEY (oauth2_client_id) REFERENCES oauth2_client (identifier)');
        $this->addSql('ALTER TABLE oauth2_client_project ADD CONSTRAINT FK_35B634FE166D1F9C FOREIGN KEY (project_id) REFERENCES project (id)');
        $this->addSql('ALTER TABLE oauth2_refresh_token ADD CONSTRAINT FK_4DD90732B6A2DD68 FOREIGN KEY (access_token) REFERENCES oauth2_access_token (identifier) ON DELETE SET NULL');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE mailer_attachment DROP FOREIGN KEY FK_7C7BB36537A1329');
        $this->addSql('ALTER TABLE mailer_transaction DROP FOREIGN KEY FK_E140622E537A1329');
        $this->addSql('ALTER TABLE oauth2_refresh_token DROP FOREIGN KEY FK_4DD90732B6A2DD68');
        $this->addSql('ALTER TABLE oauth2_access_token DROP FOREIGN KEY FK_454D9673C7440455');
        $this->addSql('ALTER TABLE oauth2_authorization_code DROP FOREIGN KEY FK_509FEF5FC7440455');
        $this->addSql('ALTER TABLE oauth2_client_project DROP FOREIGN KEY FK_35B634FE5DF1BCB8');
        $this->addSql('ALTER TABLE mailer_message DROP FOREIGN KEY FK_9880FD04166D1F9C');
        $this->addSql('ALTER TABLE oauth2_client_project DROP FOREIGN KEY FK_35B634FE166D1F9C');
        $this->addSql('DROP TABLE IF EXISTS mailer_attachment');
        $this->addSql('DROP TABLE IF EXISTS mailer_message');
        $this->addSql('DROP TABLE IF EXISTS mailer_transaction');
        $this->addSql('DROP TABLE IF EXISTS oauth2_access_token');
        $this->addSql('DROP TABLE IF EXISTS oauth2_authorization_code');
        $this->addSql('DROP TABLE IF EXISTS oauth2_client');
        $this->addSql('DROP TABLE IF EXISTS oauth2_client_project');
        $this->addSql('DROP TABLE IF EXISTS oauth2_refresh_token');
        $this->addSql('DROP TABLE IF EXISTS project');
        $this->addSql('DROP TABLE IF EXISTS messenger_messages');
    }
}
