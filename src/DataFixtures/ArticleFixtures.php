<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use App\Entity\Article;
use App\Entity\Tag;
use App\Entity\User;

class ArticleFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        $articles = [
            [
                'Cooking up memories this holiday season',
                'The kitchen is considered the heart of the home.',
                'The kitchen is considered the heart of the home and this is never so true as during 
       the holidays. For many people at this time of year, it’s important to have a place where 
       you can cook and spend time with loved ones without feeling cramped or stressed out.
       Gone are the days of the design mindset when kitchens were tucked away and used only for 
       prepping food. Today, they’re an extension of living areas. Homebuyers want beautiful 
       open spaces and expansive islands for effortless and elegant gathering. In the following 
       homes, represented by Allie Beth Allman & Associates, the kitchens will bring you joy this
        holiday season. The recently updated home at 104 Keystone Drive in Southlake’s Estes Park
         community is offered by Dona Robinson.The kitchen has a large island and new appliances 
         and connects to a double-height great room. This provides a seamless flow for large-scale 
         entertaining.Closer to Dallas’ center is the stone residence at 7130 Brookcove Lane, 
         offered by Jean Bateman. The 7,648-square-foot home includes a rustic kitchen that
          blends stone and wood and opens to a spacious living area and wet bar. The kitchen
           includes a separate baker’s cupboard.',
                '',  // imageUrl
                [1, 2], // tags
                1, 5

            ],
            [
                'The Best Black Friday Weekend Tech and Electronics Deals',
                'All products and services featured are independently chosen by editors.',
                'All products and services featured are independently chosen by editors. 
        owever, Billboard may receive a commission on orders placed through its retail links,
         and the retailer may receive certain auditable data for accounting purposes.
        Black Friday may be over but the savings continue through Black Friday weekend and 
        into Cyber Monday.
        A number of electronics brands have discounted their top tech products this weekend,
         with savings of up to $350 on top-rated TVs, headphones, earbuds and smartwatches. 
         Many of these electronics deals feature products that are being discounted for the 
         first time this year, making this weekend a great time to snag those wireless earbus or smart home device you’ve been eyeing.
        From a new $54 Amazon fitness tracker to a $45 AirPods dupe, we’ve rounded up the best tech and electronics deals to shop
         this Black Friday weekend. Quantities are starting to run out so we recommend adding to cart now.
        $101 off Sony WH-1000XM4 Wireless Headphone Sony WH-1000XM4 Wireless
        Shop: Sony WH-1000XM4 Wireless Noise-Cancelling Headphones $248
        One of the best Black Friday weekend deals is on the Sony WH-1000XM4 Wireless Headphones — the top-rated noise-cancelling headphones on the market today. Regularly $349+, the headphones are on sale for just $248 — the lowest price we’ve seen for these cans all year. See the deal here.
        $40 off Echo Show 5 ',
                '',  // imageUrl
                [1, 2], // tags
                3, 6
            ],
            [
                'Our Best Tips for Cooking With Fresh Chives—and Nope, They’re Not the Same as Scallions',
                'As we anxiously await the peak season of juicy tomatoes this August.',
                'As we anxiously await the peak season of juicy tomatoes this August, we’re still heading to our local farmer’s market to see what mid-summer produce has to offer. Among tons of bright red cherries, unique and flavorful garlic scapes, and even the early signs of fennel and arugula are the delicious and delicate chives that are perfect to add to your summer cooking. Here, everything you need to know about using chives in your cooking.

                What are chives?
                Chives are part of the onion family (called allium) and they’re most available during the warmer months, explains Debra Moser, co-founder of Central Farm Markets in Washington D.C. They’re a green, grass-like plant that, when watered and left to grow, produces a beautiful purple flower that is also edible, she adds.
                The most common type of chive you’ll see in American markets is the French chive, which looks like little green, hollow tubes of grass. But there are also Chinese chives (or garlic chives), which are flatter and have a slight garlicky taste, says Juliet Glass, director of communications at FRESHFARM, a non-profit that operates producer-only farmers’ markets in the Mid-Atlantic region.  
                They are very small and delicate but require very little prep and 
                are easy to handle. Just, simply, wash and chop to add to anything,
                 says Lee Jones, a farmer behind The Chef’s Garden.
                 Where to buy chives.You can find chives year-round at most 
                 supermarkets and in your local farmers’ market in warmer months. 
                 Most parts of the country harvest chives in their peak season of early spring (around March) through early fall (late September). According to the Farmer’s Almanac, they’re a perennial plant planted in early to mid-spring for an early summer harvest.
                Chives are also incredibly easy to grow indoors year-round,
                 so you grow your own. Moser says they turn brown at the end of the year and die back, but you can leave the shoots and they’ll sprout again the following spring as long as you continue to water them and leave them in partial shade. Jones says you can pick up a plant at the farmers’ market or grab a plant or seeds from your local plant store.',
                '',  // imageUrl
                [1, 2], // tags
                3, 8
            ]
        ];

        foreach ($articles as $article) {
            $title = $article[0];
            $subtitle = $article[1];
            $content = $article[2];
            $imageUrl = $article[3];
            $tags = $article[4];
            $color = $article[5];
            $timeToRead = $article[6];
            $article = $this->articleGenerator($title, $subtitle, $content, $imageUrl, $tags, $color, $timeToRead);
            $manager->persist($article);
        }
        $manager->flush();
    }
    public function articleGenerator(
        string $title,
        string $subtitle,
        string $content,
        string $imageUrl,
        array $tagsId,
        int $color,
        int $timeToRead
    ): Article {
        $backendTeam = $this->getReference('user1');
        $tagRef = $this->getReference('Electronics');
        //  $tagRef->setTagTitle("electronics");

        $user1 = $this->getReference('user1');
        $article = new Article();
        $article->setContent($content);
        $article->setImageUrl($imageUrl);
        $article->setTitle($title);
        $article->setColor($color);
        $article->setUser($user1);
        $article->addTagsId($tagRef);
        $article->setSubtitle($subtitle);
        $article->setTimeToRead($timeToRead);
        return $article;
    }
    public function getDependencies()
    {
        return [
            UserFixtures::class
        ];
    }
}
