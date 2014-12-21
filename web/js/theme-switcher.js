
var ThemeSwitcher =   function( select , link )
{
    var init    =   function( select , link )
    {
        $select =   $( select );
        $link   =   $( link );

        var options =   [];
        for( var i in themes )
        {
            options[i]  =   $( '<option value="'+ themes[i] +'">'+ themes[i] +'</option>' );
        }

        $select.append( options );
        $select.change(function ()
        {
            $(this).parent().submit();
            return true;
        });
    };

    var $select;
    var $link;
    var themes  =   [ 'default' , 'cerulean' , 'cosmo' , 'cyborg' , 'darkly' , 'flatly' ,
                        'journal' , 'lumen' , 'paper' , 'readable' , 'sandstone' , 'simplex' , 'slate' ,
                        'spacelab' , 'superhero' , 'united' , 'yeti' ];

    init( select , link );
};
