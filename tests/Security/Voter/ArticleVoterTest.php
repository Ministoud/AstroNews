<?php

namespace App\Tests\Security;

use App\Entity\Article;
use App\Entity\User;
use App\Security\Voter\ArticleVoter;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\VoterInterface;
use Symfony\Component\Security\Core\Security;

class ArticleVoterTest extends TestCase
{
    public function provideTestSupportsData()
    {
        return [
            [
                'isCorrectSubject' => true,
                'attribute' => 'ART_VIEW',
                'isAbstain' => false
            ],
            [
                'isCorrectSubject' => true,
                'attribute' => 'BAD_ATTRIBUTE',
                'isAbstain' => true
            ],
            [
                'isCorrectSubject' => false,
                'attribute' => 'ART_VIEW',
                'isAbstain' => true
            ]
        ];
    }

    public function provideTestConnectedData()
    {
        return [
            [
                'isConnected' => true,
                'shouldBe' => VoterInterface::ACCESS_GRANTED
            ],
            [
                'isConnected' => false,
                'shouldBe' => VoterInterface::ACCESS_DENIED
            ]
        ];
    }

    public function provideTestIsGrantedData()
    {
        return [
            [
                'authorId' => 1,
                'userID' => 2,
                'isAdmin' => true,
                'shouldBe' => VoterInterface::ACCESS_GRANTED
            ],
            [
                'authorId' => 1,
                'userID' => 2,
                'isAdmin' => false,
                'shouldBe' => VoterInterface::ACCESS_DENIED
            ],
            [
                'authorId' => 1,
                'userID' => 1,
                'isAdmin' => false,
                'shouldBe' => VoterInterface::ACCESS_GRANTED
            ],
            [
                'authorId' => 1,
                'userID' => 2,
                'isAdmin' => false,
                'shouldBe' => VoterInterface::ACCESS_DENIED
            ]
        ];
    }

    /**
     * @dataProvider provideTestSupportsData
     * @param $isCorrectSubject
     * @param $attribute
     * @param $isAbstain
     */
    public function testVoterSupportsAttribute($isCorrectSubject, $attribute, $isAbstain)
    {
        $securityMock = $this->prophesize(Security::class);
        $tokenMock = $this->prophesize(TokenInterface::class);

        $voter = new ArticleVoter($securityMock->reveal());

        if($isCorrectSubject)
        {
            $result = $voter->vote($tokenMock->reveal(), new Article(), array($attribute));
        }
        else
        {
            $result = $voter->vote($tokenMock->reveal(), new User(), array($attribute));
        }

        if($isAbstain)
        {
            $this->assertEquals(VoterInterface::ACCESS_ABSTAIN, $result);
        }
        else
        {
            $this->assertNotEquals(VoterInterface::ACCESS_ABSTAIN, $result);
        }
    }

    /**
     * @dataProvider provideTestConnectedData
     * @param $isConnected
     * @param $shouldBe
     */
    public function testVoterUserConnected($isConnected, $shouldBe)
    {
        $securityMock = $this->prophesize(Security::class);
        $securityMock->isGranted('ROLE_ADMIN')->willReturn(false);

        $tokenMock = $this->prophesize(TokenInterface::class);
        if($isConnected)
        {
            $tokenMock->getUser()->willReturn(new User());
        }

        $voter = new ArticleVoter($securityMock->reveal());

        $result = $voter->vote($tokenMock->reveal(), new Article(), array('ART_VIEW'));
        $this->assertEquals($shouldBe, $result);
    }

    /**
     * @dataProvider provideTestIsGrantedData
     * @param $authorID
     * @param $userID
     * @param $isAdmin
     * @param $shouldBe
     */
    public function testVoterUserIsAdminSuccess($authorID, $userID, $isAdmin, $shouldBe)
    {
        $author = new User();
        $author->setId($authorID);

        $user = new User();
        $user->setId($userID);

        $article = new Article();
        $article->setArtAuthor($author);

        $securityMock = $this->prophesize(Security::class);
        $securityMock->isGranted('ROLE_ADMIN')->willReturn($isAdmin);

        $tokenMock = $this->prophesize(TokenInterface::class);
        $tokenMock->getUser()->willReturn($user);

        $voter = new ArticleVoter($securityMock->reveal());

        $result = $voter->vote($tokenMock->reveal(), $article, array('ART_EDIT'));
        $this->assertEquals($shouldBe, $result);
    }
}
