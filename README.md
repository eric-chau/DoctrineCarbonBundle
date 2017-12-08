Doctrine Carbon Bundle
======================

Automatically fetch your Doctrine DateTimes as Carbon instances in Symfony.

## Installation

To install, simply run at your project root:

    composer require mnavarrocarter/doctrine-carbon-bundle
    
While composer is working, thank [@Jordi](https://twitter.com/Seldaek) and enable the bundle in your AppKernel:

    // app/AppKernel.php
    
    class AppKernel extends Kernel
    {
        public function registerBundles()
        {
            $bundles = array(
                // ...
    
                new MNC\DoctrineCarbonBundle\MNCDoctrineCarbonBundle(),
            );
    
            // ...
        }
    
        // ...
    }

## How it works

MNCDoctrineCarbonBundle leverages the popular [Carbon](https://github.com/briannesbitt/Carbon)
library as a service in order to convert your DateTime instances to Carbon ones.

By default, it listens for `createdAt`, `updatedAt` and `deletedAt` properties
in your Entities (and their getters and setters) and transforms them into Carbon
instances everytime you fetch them from the database, so you can do cool things like:

    $entity->getCreatedAt()->diffForHumans()  // Ex, Outputs '21 months ago'.
    
> Since MNCDoctrineCarbonBundle listens for those fields by default, it's a perfect companion if
you have libraries like [StofDoctrineExtensionsBundle](https://symfony.com/doc/master/bundles/StofDoctrineExtensionsBundle/index.html)
in your project.

### Specifying other properties names

If you have other DateTime fields in your entities that are not the ones mentioned
above, you can tell MNCDoctrineCarbonBundle to convert them too. Just create a public
property called `timeFields` in your entity and set it to an array of the property names you want 
to instantiate as Carbon dates, like this:

    class Post {
    
        public $timeFields = ['publishedAt'];
        
        #...
        
        /**
         * @var DateTime
         */
        private $publishedAt;
        
        #...
        
        public function getPublishedAt()
        {
            return $this->publishedAt;
        }
        
        public function setPublishedAt(DateTime $publishedAt)
        {
            $this->publishedAt = $publishedAt;
        }
    }

> Note that you need camelCase getters and setters for the properties you desire to convert in order for
MNCDoctrineCarbonBundle to do it correctly.

## What is Carbon?
Carbon is a library that serves as a wrapper to php's DateTime built-in class, providing
an easy-to-use and richly-featured API to make common operations with date and time a trivial
and delightful task.

Go thank [Brian Nesbitt](https://github.com/briannesbitt) for his awesome work!