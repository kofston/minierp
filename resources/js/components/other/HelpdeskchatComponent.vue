<template>
    <div>
        <div class="container">
            <div class="row m-0">
                <div class="col-lg-12 p-0">
                    <div class="card p-2">
                            <div>
                                <h2 style="font-size:30px;text-align:center;">Twoja dyskusja do Zamówienia: {{this.order_ident}}</h2>
                            </div>
                    </div>
                    <div class="card p-2 mt-4">
                        <span class="text-center">Moja dyskusja</span>
                        <div class="messages_container">
                        <div id="discuss"></div>
                        </div>
                        <div v-if="this.helpdesk_status == 1">
                            <label>Napisz wiadomość</label>
                            <textarea class="form-control message_text"></textarea>
                            <a href="javascript:void(0);" class="btn btn-primary d-block text-white mt-2" id="add_message" :data-helpdesk="helpdesk_id">Wyślij wiadomość / Send message</a>
                        </div>
                        <div v-if="this.helpdesk_status == 0">
                            <div class="alert alert-danger">Dyskusja do zamówienia została wstrzymana / zakończona.</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    name: "HelpdeskchatComponent",
    data() {
        return {
            helpdesk_id:'',
            order_ident:'',
            helpdesk_status:1,
        }
    },
    props: {
        query:{
            type: String,
            required:false
        }
    },
    methods:{
        sendMessage(){
            let that = this;
            $( "#add_message" ).unbind().click(function() {
                let id = $(this).data("helpdesk"),message = $(".message_text").val();
                if(message!='')
                {
                    $.ajax({
                        url: "/helpdesk/add_message/"+id,
                        data:{message:message},
                        method:"POST",
                        success: function( xhr ) {
                            console.log(xhr);
                            that.refreshChat();
                        }
                    });
                }
                else
                    alert("Brak treści wiadomości!");

            });
        },
        refreshChat()
        {
            let id = this.helpdesk_id,that = this;
            $.ajax({
                url: "/helpdesk/refresh_chat/"+id,
                method:"GET",
                success: function( xhr ) {
                   $("#discuss").html(xhr);
                }
            });
        }
    },
    beforeMount() {
        var parseQuery =  JSON.parse(this.$props.query);
        if(parseQuery.length>0)
        {
            this.helpdesk_id = parseQuery[0].helpdesk_id;
            this.order_ident = parseQuery[0].order_ident;
            this.helpdesk_status = parseQuery[0].helpdesk_status;
        }
    },
    mounted() {
        let that = this;
        this.sendMessage();
        that.refreshChat();
        var intervalId = window.setInterval(function(){
            that.refreshChat();
        }, 10000);
    }
}
</script>

<style scoped>

</style>
