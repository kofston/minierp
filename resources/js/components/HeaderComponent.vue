<template>
<div>
    <b-menu>
        <b-menu-list label="MENU">
            <a href="/home" class="p-0" title="Strona Główna"><b-menu-item icon="table" label="Strona Główna"></b-menu-item></a>
        </b-menu-list>
        <b-menu-list >
            <a href="/client" class="p-0" title="Klienci"><b-menu-item icon="user" label="Klienci"></b-menu-item></a>
        </b-menu-list>
        <b-menu-list >
            <a href="/product" class="p-0" title="Produkty"><b-menu-item icon="folder" label="Produkty"></b-menu-item></a>
        </b-menu-list>
        <b-menu-list>
            <a href="/order" class="p-0" title="Zamówienia"><b-menu-item icon="box" label="Zamówienia"></b-menu-item></a>
        </b-menu-list>
        <b-menu-list>
            <a href="/delivery" class="p-0" title="Delivery"><b-menu-item icon="cubes" label="Przesyłki"></b-menu-item></a>
        </b-menu-list>
        <b-menu-list>
            <a href="/helpdesk" class="p-0" title="HelpDesk"><b-menu-item icon="info" label="Help Desk"></b-menu-item></a>
        </b-menu-list>
        <b-menu-list>
            <a href="/offer" class="p-0" title="Oferty"><b-menu-item icon="tag" label="Oferty"></b-menu-item></a>
        </b-menu-list>
    </b-menu>
</div>
</template>

<script>
export default {
    name: "HeaderComponent"
}
</script>

<style scoped>

</style>
