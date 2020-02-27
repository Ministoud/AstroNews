<?php

namespace App\Tests\Security;

use App\Entity\Article;
use App\Entity\User;
use App\Security\Voter\UserVoter;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\VoterInterface;
use Symfony\Component\Security\Core\Security;

class UserVoterTest extends TestCase
{
    public function provideTestSupportsData()
    {
        return [
            [
                'subject' => new User(),
                'attribute' => 'USE_VIEW',
                'isAbstain' => false
            ],
            [
                'subject' => new User(),
                'attribute' => 'BAD_ATTRIBUTE',
                'isAbstain' => true
            ],
            [
                'subject' => new Article(),
                'attribute' => 'USE_VIEW',
                'isAbstain' => true
            ]
        ];
    }

    public function provideTestConnectedData()
    {
        return [
            [
                'isConnected' => true,
                'expected' => VoterInterface::ACCESS_GRANTED
            ],
            [
                'isConnected' => false,
                'expected' => VoterInterface::ACCESS_DENIED
            ]
        ];
    }

    public function provideTestIsGrantedData()
    {
        return [
            [
                'isAdmin' => true,
                'expected' => VoterInterface::ACCESS_GRANTED
            ],
            [
                'isAdmin' => false,
                'expected' => VoterInterface::ACCESS_DENIED
            ],
            [
                'isAdmin' => false,
                'expected' => VoterInterface::ACCESS_DENIED
            ]
        ];
    }

    /**
     * @dataProvider provideTestSupportsData
     * @param $subject
     * @param $attribute
     * @param $isAbstain
     */
    public function testVoterSupports($subject, $attribute, $isAbstain)
    {
        $securityMock = $this->prophesize(Security::class);
        $tokenMock = $this->prophesize(TokenInterface::class);

        $voter = new UserVoter($securityMock->reveal());

        $result = $voter->vote($tokenMock->reveal(), $subject, array($attribute));

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
     * @param $expected
     */
    public function testVoterUserConnected($isConnected, $expected)
    {
        $securityMock = $this->prophesize(Security::class);
        $securityMock->isGranted('ROLE_ADMIN')->willReturn(false);

        $tokenMock = $this->prophesize(TokenInterface::class);
        if($isConnected)
        {
            $tokenMock->getUser()->willReturn(new User());
        }

        $voter = new UserVoter($securityMock->reveal());

        $result = $voter->vote($tokenMock->reveal(), new User(), array('USE_VIEW'));
        $this->assertEquals($expected, $result);
    }

    /**
     * @dataProvider provideTestIsGrantedData
     * @param $isAdmin
     * @param $expected
     */
    public function testVoterUserEditGranted($isAdmin, $expected)
    {
        $user = new User();

        $securityMock = $this->prophesize(Security::class);
        $securityMock->isGranted('ROLE_ADMIN')->willReturn($isAdmin);

        $tokenMock = $this->prophesize(TokenInterface::class);
        $tokenMock->getUser()->willReturn($user);

        $voter = new UserVoter($securityMock->reveal());

        $result = $voter->vote($tokenMock->reveal(), $user, array('USE_EDIT'));
        $this->assertEquals($expected, $result);
    }
}
