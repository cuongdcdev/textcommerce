**Introduction**
Hi everyone. My name’s Levi. I’m a freelance web developer . I started using TextPattern for my sites about six months ago and I’m hooked. I get regular requests to build online stores and, for lack of a better option, I’ve been using DigiShop for all my clients. I found myself wishing that there was a solution that managed stores as well as Textpattern manages sites. Then it hit me: why can’t TXP manage stores?
Why not another solution?

The only two open-source solutions on the market are osCommerce and Zen Cart and they share the same code base. Both are over-blown and archaic, completely lacking in elegance. Speaking from experience, customizing one of these solutions is a nightmare of digging through miles of code circa 1999. Most people go with basic variations of the default install.





**So why Textpattern?**
I doubt that I need to reiterate the benefits of using Textpattern to build a website. It just makes it easier. I believe that with a few plugins the same benefits could be applied to building an online store. The store plugin would focus specifically on taking advantage of the following features:





**Custom Admin Interface**
Because Textpattern allows users to write admin plugins the store would have its own management section separate from the rest of the TextPattern functionality. I’m thinking it would be a peer tab to ‘content’ and ‘presentation’. This would give developers the ability to let store content managers to administer their store. This admin section would handle the following items:

  1. Product categories
> 2. Product management (standard CRUD functions)
> 3. Customer management
> 4. Order management
> 5. Store settings






**The Plugin API**
Standard E-Commerce components would be built as individual plugins that don’t depend on each other. So if you wanted to have a custom payment/checkout process but also wanted use other existing plugins you could just write a new plugin and replace the tag without breaking any of the store’s other functionality.
Products would be Articles

Normal product attributes such as weight and price would be custom fields. This way store developers can take advantages of the wealth of tags and functionality available to articles.