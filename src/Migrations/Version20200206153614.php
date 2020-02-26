<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200206153614 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE user_section (user_id INT NOT NULL, section_id INT NOT NULL, INDEX IDX_757E64E5A76ED395 (user_id), INDEX IDX_757E64E5D823E37A (section_id), PRIMARY KEY(user_id, section_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE user_section ADD CONSTRAINT FK_757E64E5A76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE user_section ADD CONSTRAINT FK_757E64E5D823E37A FOREIGN KEY (section_id) REFERENCES section (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE article ADD art_author_id INT NOT NULL');
        $this->addSql('ALTER TABLE article ADD CONSTRAINT FK_23A0E662F84133 FOREIGN KEY (art_author_id) REFERENCES user (id)');
        $this->addSql('CREATE INDEX IDX_23A0E662F84133 ON article (art_author_id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE user_section');
        $this->addSql('ALTER TABLE article DROP FOREIGN KEY FK_23A0E662F84133');
        $this->addSql('DROP INDEX IDX_23A0E662F84133 ON article');
        $this->addSql('ALTER TABLE article DROP art_author_id');
    }
}
