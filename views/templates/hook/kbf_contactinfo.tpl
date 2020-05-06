<div id="kbf_contactinfo" class="fullwidth">
    
    {if $contact_data['isEmail'] || $contact_data['isPhone']}
        <span class="contactinfo">
        {l s="BOK:" mod="kb_footercontactinfo"}

        {if $contact_data['isPhone']}

            {$contact_data['phone']}

            {if $contact_data['isEmail']}
                <span class="separator"> | </span>
            {/if}
        {/if}
        {if $contact_data['isEmail']}
            {$contact_data['email']}
        {/if}
        </span>
    {/if}
    

    {if $contact_data['text']}
        <span class="textinfo">{$contact_data['text']}</span>
    {/if}   
</div>
{* Styles in scss _dev *}

{* #kbf_contactinfo{
    padding: $spacer $spacer/2;
    display: flex;
    justify-content: space-evenly;
    flex-wrap: inherit;

    background-color: $crea-green;
    color: #fff;

    font-weight: 600;
}

@media (max-width: 575px){
    #kbf_contactinfo{
        text-align: center;
        .contactinfo{
            .separator{
                display: none;
            }
        }
    }
} *}