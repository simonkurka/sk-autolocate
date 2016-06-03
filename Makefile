include $(TOPDIR)/rules.mk

PKG_NAME:=sk-autolocate
PKG_VERSION:=1
PKG_RELEASE:=1

PKG_BUILD_DIR := $(BUILD_DIR)/$(PKG_NAME)
PKG_BUILD_DEPENDS := 

include $(GLUONDIR)/include/package.mk

define Package/sk-autolocate
  SECTION:=gluon
  CATEGORY:=Gluon
  TITLE:=Add autolocate functionality
  DEPENDS:=+gluon-node-info
endef

define Build/Prepare
endef

define Package/sk-autolocate/install
	$(CP) ./files/* $(1)/
endef

define Package/sk-autolocate/postinst
#!/bin/sh
$(call GluonCheckSite,check_site.lua)
endef

$(eval $(call BuildPackage,sk-autolocate))
