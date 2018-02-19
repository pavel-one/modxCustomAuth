{* id 18 change *}


{if !$.session.pass_hash}
    Это служебная страница, ее посещать не надо
{elseif $.session.pass_hash == $.get.hash}
    <form style="text-align:  center;" id="rememberme">
        <input type="text" style="border:  none;padding:  10px 15px;box-shadow: 3px 3px 10px rgba(0,0,0,.15);" name="newpass" placeholder="Введите новый пароль">
        <input type="hidden" name="hash" value="{$.get.hash}">
        <input type="hidden" name="userid" value="{$.get.user}">
        <button style="background:  #33b2cf;color:  #fff;border:  none;padding:  10px 15px;box-shadow: 3px 3px 10px rgba(0,0,0,.15);margin-left:  10px;">Сохранить!</button>
    </form>
{else}
    Неизвестная ошибка
{/if}