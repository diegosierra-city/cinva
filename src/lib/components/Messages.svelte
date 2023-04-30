<script lang="ts">
   import type {Message} from '$lib/types/Message'
   
    export let message: Message
    export let m_show: Boolean
    
    const cerrar = () => {
        //alert('u')
        m_show= false
    }

    $: if(m_show==true){
        setTimeout(function () {
            m_show = false;
        }, 5000); /// cierre automático
    }
    </script>
    
    
         {#if message && m_show==true}
         
         <!-- svelte-ignore a11y-click-events-have-key-events -->
         <div class="bg-message " on:click|self={()=>cerrar()}>
            <div id="znMessage" class={message.class}>
                <div class="message-title" >
                    <h3>{ message.title }</h3>
                </div>
                
                <p class="text-sm text-gray pb-3 font-normal message-body">{ message.text }</p>
                
                {#if message.accion!=''}
                <button class="btn-green"  on:click={()=>{message.accion}}>Aceptar</button>
                <button class="btn-red"  on:click={cerrar}>Cancelar</button>
                {:else}
                <button class="btn-primary"  on:click={cerrar}>Cerrar</button>
                {/if}
                
                  
            </div>
            </div>
         {/if}
   
    
   